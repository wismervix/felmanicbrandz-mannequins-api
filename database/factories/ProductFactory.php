<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['mannequins', 'hangers'];
        $brands = ['Essence', 'Glamour Beauty', 'Velvet Touch', 'Acme', 'Umbrella'];
        $availability = ['In Stock', 'Out of Stock', 'Preorder'];

        // Pick a category
        $category = $this->faker->randomElement($categories);

        // Assign valid images based on category
        $categoryImages = [
            'mannequins' => [
                'https://images.unsplash.com/photo-1770529933763-2d8b94fe6ba9?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzV8fG1hbm5lcXVpbnN8ZW58MHx8MHx8fDA%3D',
                'https://images.unsplash.com/photo-1729941346784-1710d3e75ac2?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fG1hbm5lcXVpbnN8ZW58MHx8MHx8fDA%3D',
                'https://plus.unsplash.com/premium_photo-1677838847808-686ac388d5f6?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8bWFubmVxdWluc3xlbnwwfHwwfHx8MA%3D%3D',
            ],
            'hangers' => [
                'https://plus.unsplash.com/premium_photo-1672883552008-e40d0a48fcbd?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8aGFuZ2Vyc3xlbnwwfHwwfHx8MA%3D%3D',
                'https://images.unsplash.com/photo-1519220279207-fddf068f2141?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8aGFuZ2Vyc3xlbnwwfHwwfHx8MA%3D%3D',
                'https://images.unsplash.com/photo-1584467331486-29e6c30262c3?w=800&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8aGFuZ2Vyc3xlbnwwfHwwfHx8MA%3D%3D',
            ],
        ];

        // Choose 1-3 images from category
        $numImages = $this->faker->numberBetween(1, 3);
        $images = $this->faker->randomElements(
            $categoryImages[$category],
            min($numImages, count($categoryImages[$category]))
        );

        // Pick first as thumbnail
        $thumbnail = $images[0];

        // Fake reviews
        $reviews = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 3); $i++) {
            $reviews[] = [
                'rating' => $this->faker->numberBetween(1, 5),
                'comment' => $this->faker->sentence(),
                'date' => $this->faker->iso8601(),
                'reviewerName' => $this->faker->name(),
                'reviewerEmail' => $this->faker->unique()->safeEmail(),
            ];
        }

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'category' => $category,
            'price' => $this->faker->randomFloat(2, 5, 200),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 50),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'stock' => $this->faker->numberBetween(0, 100),
            'tags' => $this->faker->words(2),
            'brand' => $this->faker->randomElement($brands),
            'sku' => $this->faker->unique()->bothify('???-???-###'),
            'weight' => $this->faker->numberBetween(1, 50),
            'dimensions' => [
                'width' => $this->faker->randomFloat(2, 5, 30),
                'height' => $this->faker->randomFloat(2, 5, 30),
                'depth' => $this->faker->randomFloat(2, 5, 30),
            ],
            'warranty_information' => $this->faker->sentence(),
            'shipping_information' => $this->faker->sentence(),
            'availability_status' => $this->faker->randomElement($availability),
            'reviews' => $reviews,
            'return_policy' => $this->faker->sentence(),
            'minimum_order_quantity' => $this->faker->numberBetween(1, 50),
            'meta' => [
                'createdAt' => now()->toIso8601String(),
                'updatedAt' => now()->toIso8601String(),
                'barcode' => $this->faker->ean13(),
                'qrCode' => 'https://cdn.dummyjson.com/public/qr-code.png',
            ],
            'images' => $images,
            'thumbnail' => $thumbnail,
        ];
    }
}
