<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SteeringDocUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_upload_steering_doc_folder()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('steering.upload'), [
            'folder_type' => 'claude',
            'files' => [
                'instructions.md' => UploadedFile::fake()->create('instructions.md', 10, 'text/markdown'),
                'settings.json' => UploadedFile::fake()->create('settings.json', 5, 'application/json'),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('steering_collections', [
            'user_id' => $user->id,
            'type' => 'claude',
        ]);
    }

    /** @test */
    public function detects_claude_folder_structure()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('steering.upload'), [
            'files' => [
                'instructions.md' => UploadedFile::fake()->create('instructions.md', 10),
                'settings.json' => UploadedFile::fake()->create('settings.json', 5),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('steering_collections', [
            'type' => 'claude',
        ]);
    }

    /** @test */
    public function detects_kiro_folder_structure()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('steering.upload'), [
            'files' => [
                'AGENT_IDENTITY.md' => UploadedFile::fake()->create('AGENT_IDENTITY.md', 10),
                'QUICK_REFERENCE.md' => UploadedFile::fake()->create('QUICK_REFERENCE.md', 5),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('steering_collections', [
            'type' => 'kiro',
        ]);
    }

    /** @test */
    public function stores_individual_steering_docs()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('steering.upload'), [
            'folder_type' => 'claude',
            'files' => [
                'instructions.md' => UploadedFile::fake()->create('instructions.md', 10),
                'rules/testing.md' => UploadedFile::fake()->create('testing.md', 5),
            ],
        ]);

        $this->assertDatabaseHas('steering_docs', [
            'file_path' => 'instructions.md',
        ]);
        
        $this->assertDatabaseHas('steering_docs', [
            'file_path' => 'rules/testing.md',
        ]);
    }

    /** @test */
    public function free_tier_limited_to_one_steering_collection()
    {
        Storage::fake('local');
        $user = User::factory()->create(['subscription_tier' => 'free']);

        // First upload succeeds
        $this->actingAs($user)->post(route('steering.upload'), [
            'folder_type' => 'claude',
            'files' => [
                'instructions.md' => UploadedFile::fake()->create('instructions.md', 10),
            ],
        ]);

        // Second upload fails
        $response = $this->actingAs($user)->post(route('steering.upload'), [
            'folder_type' => 'kiro',
            'files' => [
                'AGENT_IDENTITY.md' => UploadedFile::fake()->create('AGENT_IDENTITY.md', 10),
            ],
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function pro_tier_can_upload_ten_steering_collections()
    {
        Storage::fake('local');
        $user = User::factory()->create(['subscription_tier' => 'pro']);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($user)->post(route('steering.upload'), [
                'folder_type' => 'claude',
                'files' => [
                    "instructions-{$i}.md" => UploadedFile::fake()->create("instructions-{$i}.md", 10),
                ],
            ]);
            $response->assertRedirect();
        }

        // 11th fails
        $response = $this->actingAs($user)->post(route('steering.upload'), [
            'folder_type' => 'claude',
            'files' => [
                'instructions-11.md' => UploadedFile::fake()->create('instructions-11.md', 10),
            ],
        ]);

        $response->assertStatus(403);
    }
}
