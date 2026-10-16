<?php

use App\Events\MarkdownChanged;
use App\Listeners\PublishMarkdownChangeToForge;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

test('markdown changes publish a content-addressed document to Forge', function () {
    config()->set('services.moof_forge.url', 'http://git.moof.local/api/v1');
    config()->set('services.moof_forge.token', 'test-token');

    Http::fake();

    $event = new MarkdownChanged(
        source: 'github',
        repository: 'deanhowe/moof',
        path: '.knowledge/BOOT.md',
        content: '# Forge brain',
        contentHash: hash('sha256', '# Forge brain'),
        changedAt: '2026-10-15T20:00:00Z',
        commit: 'abc123',
        metadata: ['change_type' => 'crawled'],
    );

    app(PublishMarkdownChangeToForge::class)->handle($event);

    Http::assertSent(function ($request) use ($event) {
        $payload = $request->data();
        return $request->url() === 'http://git.moof.local/api/v1/creative/documents'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $payload['content_id'] === 'content:' . $event->contentHash
            && $payload['subject_id'] === 'deanhowe/moof:.knowledge/BOOT.md'
            && $payload['metadata']['commit'] === 'abc123'
            && $payload['text'] === '# Forge brain';
    });
});
test('markdown changes remain observable without Forge credentials', function () {
    config()->set('services.moof_forge.url', 'http://git.moof.local/api/v1');
    config()->set('services.moof_forge.token', null);

    Http::fake();

    app(PublishMarkdownChangeToForge::class)->handle(new MarkdownChanged(
        source: 'filesystem',
        repository: 'markdown.observer',
        path: 'notes.md',
        content: 'changed',
        contentHash: hash('sha256', 'changed'),
        changedAt: '2026-10-15T20:00:00Z',
    ));

    Http::assertNothingSent();
});
