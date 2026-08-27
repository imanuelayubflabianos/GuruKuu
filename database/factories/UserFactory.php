<?php
// database/factories/UserFactory.php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'siswa',
            'nis' => fake()->unique()->numerify('2024######'),
            'phone' => fake()->phoneNumber(),
            'jurusan_id' => Jurusan::inRandomOrder()->first()?->id,
            'kelas' => fake()->randomElement(['X', 'XI', 'XII']) . ' ' . fake()->randomElement(['1', '2', '3']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => [
            'role' => 'admin',
            'email' => fake()->unique()->safeEmail(),
            'nis' => null,
            'jurusan_id' => null,
            'kelas' => null,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }
}