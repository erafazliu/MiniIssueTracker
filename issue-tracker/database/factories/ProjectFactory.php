<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $projectNames = [
            'Website Redesign',
            'Mobile App',
            'API Integration',
            'Dashboard Overhaul',
            'E-Commerce Platform',
            'CRM System',
            'Analytics Tool',
            'Internal Tools',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($projectNames).' '.$this->faker->year(),
            'description' => $this->faker->sentence(),
        ];
    }
}
