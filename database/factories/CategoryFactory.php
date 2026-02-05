<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Fiksi', 'Non-Fiksi', 'Sains', 'Teknologi',
            'Sejarah', 'Biografi', 'Pendidikan', 'Agama',
            'Ekonomi', 'Hukum', 'Kesehatan', 'Seni & Budaya'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'icon' => 'bi-book',
            'is_active' => true,
        ];
    }
}
