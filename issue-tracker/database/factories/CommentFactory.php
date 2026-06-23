<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        $comments = [
            'I agree with this approach. Let me know if you need any help implementing it.',
            'Good catch! We should prioritize this.',
            'I think we can solve this with the existing architecture.',
            'Let me review the implementation and get back to you.',
            'This looks good to me. Ready to merge when you are.',
            'Can we get more details on the expected behavior?',
            'I suggest we break this into smaller tasks.',
            'Great work on the implementation!',
            'Let me know when this is ready for testing.',
            'This needs to be tested on all browsers.',
            'I think we should add unit tests for this.',
            'Can we schedule a quick sync to discuss?',
            'This looks like a good improvement.',
            'I recommend adding error handling here.',
            'Let me see if there are any edge cases we missed.',
        ];

        return [
            'body' => $this->faker->randomElement($comments),
        ];
    }
}
