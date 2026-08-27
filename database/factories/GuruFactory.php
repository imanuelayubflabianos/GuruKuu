<?php
// database/factories/GuruFactory.php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {
        return [
            'nip' => $this->faker->unique()->numerify('19##########'),
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'kategori' => $this->faker->randomElement(['normada', 'produktif']),
            'jurusan_id' => Jurusan::inRandomOrder()->first()?->id,
            'bio' => $this->faker->sentence(10),
            'rata_rata_nilai' => $this->faker->randomFloat(2, 3.5, 5),
            'total_penilaian' => $this->faker->numberBetween(10, 100),
        ];
    }

    public function normada(): static
    {
        return $this->state(fn() => ['kategori' => 'normada']);
    }

    public function produktif(): static
    {
        return $this->state(fn() => ['kategori' => 'produktif']);
    }
}