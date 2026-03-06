<?php

namespace Database\Factories;

use Database\Seeders\ProductSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['user', 'admin', 'moderator'];
        $genders = ['male', 'female', 'other'];

        $age = $this->faker->numberBetween(18, 60);
        $birthDate = now()->subYears($age)->format('Y-m-d');
        $imageUrl = $this->faker->imageUrl(200, 200);

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'age' => $age,
            'gender' => $this->faker->randomElement($genders),
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->unique()->userName,
            'password' => '$2y$12$2P7sY2GKEzNZ7SYniDSGlOR22ZuYqqC0AiPNREtXWJxidVfloLmiq', // fixed hashed password
            'birthDate' => $birthDate,
            'image' => $imageUrl,
            'role' => $this->faker->randomElement($roles),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
