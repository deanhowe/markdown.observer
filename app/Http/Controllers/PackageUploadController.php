<?php

namespace App\Http\Controllers;

use App\Models\UserPackage;
use App\Models\PackageDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PackageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        $user = auth()->user();
        
        // Check upload limit
        $uploadCount = UserPackage::where('user_id', $user->id)->distinct('type')->count('type');
        if ($uploadCount >= $user->upload_limit) {
            return back()->withErrors(['error' => 'Upload limit reached. Upgrade to add more!']);
        }

        $packages = [];
        $file = $request->file('file');
        $content = json_decode($file->get(), true);

        // Determine file type and parse
        if ($file->getClientOriginalName() === 'composer.json' || isset($content['require'])) {
            // Parse composer.json
            foreach ($content['require'] ?? [] as $name => $version) {
                // Filter out php and extensions
                if (str_contains($name, '/')) {
                    $packages[] = ['name' => $name, 'version' => $version, 'type' => 'composer'];
                }
            }
        } elseif ($file->getClientOriginalName() === 'package.json' || isset($content['dependencies'])) {
            // Parse package.json
            foreach ($content['dependencies'] ?? [] as $name => $version) {
                $packages[] = ['name' => $name, 'version' => $version, 'type' => 'npm'];
            }
        }

        // Show selection page
        $currentCount = UserPackage::where('user_id', $user->id)->count();
        
        return Inertia::render('SelectPackages', [
            'packages' => $packages,
            'limits' => [
                'can_add_more' => $currentCount < $user->doc_limit,
                'current_count' => $currentCount,
                'limit' => $user->doc_limit,
            ],
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'packages' => 'required|array',
            'packages.*.name' => 'required|string',
            'packages.*.version' => 'required|string',
            'packages.*.type' => 'required|in:composer,npm',
        ]);

        $user = auth()->user();
        $currentCount = UserPackage::where('user_id', $user->id)->count();
        
        // Check limit
        if ($currentCount + count($request->packages) > $user->doc_limit) {
            return back()->withErrors(['error' => 'Package limit exceeded!']);
        }

        // Store selected packages and queue doc fetching
        foreach ($request->packages as $packageData) {
            $package = UserPackage::firstOrCreate([
                'user_id' => $user->id,
                'package_name' => $packageData['name'],
            ], [
                'version' => $packageData['version'],
                'type' => $packageData['type'],
            ]);
            
            // Queue doc fetching
            \App\Jobs\FetchPackageDocsJob::dispatch($package);
        }

        return redirect()->route('dashboard')->with('success', count($request->packages) . ' packages added! Docs are being fetched...');
    }

    public function viewDocs(string $packageName)
    {
        $package = UserPackage::where('user_id', auth()->id())
            ->where('package_name', $packageName)
            ->firstOrFail();

        $docs = PackageDoc::where('user_id', auth()->id())
            ->where('package_name', $packageName)
            ->get();

        return Inertia::render('PackageDocs', [
            'package' => $package,
            'docs' => $docs,
        ]);
    }

    public function updateDoc(Request $request, int $docId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $doc = PackageDoc::where('user_id', auth()->id())
            ->findOrFail($docId);

        $doc->update([
            'content' => $request->content,
            'is_edited' => $doc->original_content !== $request->content,
        ]);

        return back()->with('success', 'Doc updated!');
    }

    public function sync(string $package)
    {
        $userPackage = UserPackage::where('user_id', auth()->id())
            ->where('package_name', $package)
            ->firstOrFail();

        // Queue doc fetching
        \App\Jobs\FetchPackageDocsJob::dispatch($userPackage);

        return response()->json(['message' => 'Sync queued!']);
    }

    private function fetchDocsFromGitHub(UserPackage $package)
    {
        $repo = $package->package_name;
        $branch = 'master'; // Try master first, fallback to main
        
        // Try to fetch docs directory listing
        $response = Http::get("https://api.github.com/repos/{$repo}/contents/docs?ref={$branch}");
        
        if ($response->failed()) {
            // Try main branch
            $response = Http::get("https://api.github.com/repos/{$repo}/contents/docs?ref=main");
            $branch = 'main';
        }
        
        if ($response->successful()) {
            $files = $response->json();
            
            foreach ($files as $file) {
                if ($file['type'] === 'file' && str_ends_with($file['name'], '.md')) {
                    // Fetch file content
                    $content = Http::get($file['download_url'])->body();
                    
                    PackageDoc::updateOrCreate(
                        [
                            'user_id' => $package->user_id,
                            'package_name' => $package->package_name,
                            'file_path' => 'docs/' . $file['name'],
                        ],
                        [
                            'content' => $content,
                            'original_content' => $content,
                            'upstream_hash' => md5($content),
                            'is_edited' => false,
                        ]
                    );
                }
            }
        }
        
        // Always fetch README
        $readme = Http::get("https://raw.githubusercontent.com/{$repo}/{$branch}/README.md");
        if ($readme->successful()) {
            PackageDoc::updateOrCreate(
                [
                    'user_id' => $package->user_id,
                    'package_name' => $package->package_name,
                    'file_path' => 'README.md',
                ],
                [
                    'content' => $readme->body(),
                    'original_content' => $readme->body(),
                    'upstream_hash' => md5($readme->body()),
                    'is_edited' => false,
                ]
            );
        }
    }
}
