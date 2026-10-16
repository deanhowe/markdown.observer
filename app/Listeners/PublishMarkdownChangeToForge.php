<?php

namespace App\Listeners;

use App\Events\MarkdownChanged;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Sends the normalized change to Forge's creative document projection.
 *
 * No Forge credential is committed here.  A configured environment token is
 * preferred; on the local iMac the existing machine Keychain item is used as
 * the same private runtime reference used by the other Moof clients.
 */
class PublishMarkdownChangeToForge
{
    public function handle(MarkdownChanged $event): void
    {
        $baseUrl = rtrim((string) config('services.moof_forge.url'), '/');
        $token = $this->token();
        if ($baseUrl === '' || $token === '') {
            Log::debug('Markdown change observed without Forge publication credentials', [
                'source' => $event->source,
                'repository' => $event->repository,
                'path' => $event->path,
                'content_hash' => $event->contentHash,
            ]);
            return;
        }

        $payload = [
            'subject_type' => 'markdown',
            'subject_id' => $event->repository . ':' . $event->path,
            'content_id' => 'content:' . $event->contentHash,
            'kind' => 'markdown',
            'title' => basename($event->path),
            'text' => $event->content,
            'source' => 'markdown.observer:' . $event->source,
            'metadata' => array_merge($event->metadata, [
                'repository' => $event->repository,
                'path' => $event->path,
                'source' => $event->source,
                'commit' => $event->commit,
                'changed_at' => $event->changedAt,
            ]),
        ];

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(5)
            ->post($baseUrl . '/creative/documents', $payload);

        if (! $response->successful()) {
            Log::warning('Forge rejected Markdown change publication', [
                'status' => $response->status(),
                'repository' => $event->repository,
                'path' => $event->path,
                'content_hash' => $event->contentHash,
            ]);
        }
    }

    /**
     * Keep the credential out of the repository and local .env files while
     * still allowing the local Markdown observer to publish automatically.
     * The Forge MCP and OXYGEN already use this machine-local Keychain item.
     * Production/test environments remain environment-only by design.
     */
    private function token(): string
    {
        $configured = trim((string) config('services.moof_forge.token'));
        if ($configured !== '' || ! app()->environment('local')) {
            return $configured;
        }

        $process = new Process([
            '/usr/bin/security',
            'find-generic-password',
            '-s', 'gitea-token-dean',
            '-a', 'dean',
            '-w',
        ]);
        $process->setTimeout(2);
        $process->run();

        return $process->isSuccessful() ? trim($process->getOutput()) : '';
    }
}
