<?php

namespace App\Http\Controllers;

use App\Models\SteeringCollection;
use App\Models\SteeringDoc;
use Illuminate\Http\Request;

class SteeringDocController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // 10MB max per file
            'folder_type' => 'nullable|string|in:claude,cursor,kiro,ai,aider,windsurf',
        ]);

        // Check subscription limits
        $user = auth()->user();
        $limit = match($user->subscription_tier) {
            'free' => 1,
            'pro' => 10,
            'lifetime' => PHP_INT_MAX,
            default => 1,
        };

        if ($user->steeringCollections()->count() >= $limit) {
            abort(403, 'Steering collection limit reached. Upgrade to add more.');
        }

        // Detect folder type from files if not provided
        $folderType = $request->folder_type ?? $this->detectFolderType($request->file('files'));

        // Create collection
        $collection = SteeringCollection::create([
            'user_id' => $user->id,
            'name' => "Uploaded {$folderType} docs",
            'type' => $folderType,
            'is_public' => false,
        ]);

        // Store files
        foreach ($request->file('files') as $path => $file) {
            SteeringDoc::create([
                'steering_collection_id' => $collection->id,
                'file_path' => $path,
                'content' => file_get_contents($file->getRealPath()),
                'is_edited' => false,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Steering docs uploaded!');
    }

    private function detectFolderType(array $files): string
    {
        $filenames = array_keys($files);
        
        // Claude: instructions.md, settings.json
        if (in_array('instructions.md', $filenames) || in_array('settings.json', $filenames)) {
            return 'claude';
        }
        
        // Kiro: AGENT_IDENTITY.md, QUICK_REFERENCE.md
        if (in_array('AGENT_IDENTITY.md', $filenames) || in_array('QUICK_REFERENCE.md', $filenames)) {
            return 'kiro';
        }
        
        // Default
        return 'ai';
    }
}
