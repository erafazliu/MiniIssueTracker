<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    public function definition(): array
    {
        $issueTitles = [
            'Fix login error on mobile',
            'Implement dark mode',
            'Improve page load performance',
            'Add two-factor authentication',
            'Update user profile page',
            'Fix broken links in footer',
            'Optimize database queries',
            'Create admin dashboard',
            'Add export to PDF feature',
            'Improve error messages',
            'Update API documentation',
            'Fix email notifications',
            'Add search functionality',
            'Update design system',
            'Refactor authentication',
        ];

        return [
            'title' => $this->faker->randomElement($issueTitles),
            'description' => $this->faker->sentence(12),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween('+1 day', '+3 months'),
        ];
    }
}
