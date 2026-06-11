<?php

namespace Database\Factories;

use App\Models\FoodClaim;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    private static array $reviews = [
        'Makanan sangat enak dan segar, terima kasih donaturnya!',
        'Pelayanan ramah, makanan masih hangat saat diambil.',
        'Sangat membantu, makanannya lezat dan bergizi.',
        'Prosesnya mudah dan cepat. Makanan berkualitas bagus.',
        'Terima kasih banyak, sangat bermanfaat bagi kami.',
        'Makanan dalam kondisi baik, rasa enak.',
        'Mantap, akan kembali lagi jika ada makanan tersedia.',
        'Donatur yang dermawan, semoga rezekinya berlipat ganda.',
    ];

    public function definition(): array
    {
        return [
            'food_id' => 1, // will be overridden
            'claim_id' => FoodClaim::factory()->completed(),
            'user_id' => 1, // will be overridden
            'rating' => fake()->numberBetween(3, 5),
            'review' => fake()->randomElement(self::$reviews),
        ];
    }
}
