<?php

namespace Rakutentech\LaravelRequestDocs\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rakutentech\LaravelRequestDocs\Tests\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<User>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'John Doe',
            'email' => 'johndoe@email.com',
        ];
    }
}
