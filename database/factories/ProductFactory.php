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
        $categories = ['beauty', 'electronics', 'fashion'];
        $brands = ['Essence', 'Glamour Beauty', 'Velvet Touch', 'Acme', 'Umbrella'];
        $availability = ['In Stock', 'Out of Stock', 'Preorder'];

        // Pick a category
        $category = $this->faker->randomElement($categories);

        // Assign valid images based on category
        $categoryImages = [
            'beauty' => [
                'https://images.pexels.com/photos/beauty-products-1.jpg',
                'https://images.pexels.com/photos/beautiful-cosmetics-on-shelf.jpg',
                'https://images.unsplash.com/photo-1514996937319-344454492b37?fit=crop&w=800&q=80',
            ],
            'electronics' => [
                'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?fit=crop&w=800&q=80',
                'https://unsplash.com/photos/gadgets-01.jpg',
            ],
            'fashion' => [
                'https://images.unsplash.com/photo-1521334884684-d80222895322?fit=crop&w=800&q=80',
                'https://images.pexels.com/photos/fashion-trend-2.jpg',
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
