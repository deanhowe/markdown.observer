<?php

namespace Database\Factories;

use App\Models\PageRevision;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageRevisionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PageRevision::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'filename' => $this->faker->slug(2) . '.md',
            'markdown_content' => '# ' . $this->faker->sentence,
            'html_content' => '<h1>' . $this->faker->sentence . '</h1>',
            'tiptap_json' => ['type' => 'doc', 'content' => []],
            'revision_type' => $this->faker->randomElement(['create', 'update', 'delete', 'conflict']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
