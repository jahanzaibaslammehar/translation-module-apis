<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => $this->faker->languageCode,
            'context' => $this->faker->randomElement(['mobile', 'web', 'desktop']),
            'translations' => [
                        [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                        [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                        [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                        [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
                                                [
                        'key' => str_replace(' ', '_', $this->faker->words(3, true)),
                        'translation' => $this->faker->sentence(),
                        ],
            ],
        ];
    }
}