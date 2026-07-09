<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

it('edits an existing idea', function () {
    $this->actingAs($user = User::factory()->create());

    $idea = Idea::factory()->for($user)->create();

    $title = 'Example title';
    $description = 'Example description';
    $link = 'https://example.com';
    $stepDescription = 'To do something.';

    visit(route('idea.show', $idea))
        ->click('@edit-idea-button')
        ->fill('title', $title)
        ->click('@button-status-completed')
        ->fill('description', $description)
        ->fill('@new-link', $link)
        ->click('@submit-new-link')

        ->fill('@new-step', $stepDescription)
        ->click('@submit-new-step')

        ->click('Update')
        ->assertRoute('idea.show', [$idea]);

    expect($idea = $user->ideas()->first())->toMatchArray([
        'title' => $title,
        'status' => IdeaStatus::COMPLETED->value,
        'description' => $description,
        'links' => [$idea->links[0], $link],
    ])
        ->and($idea->steps()->first())->toMatchArray([
            'description' => $stepDescription,
        ])
        ->and($idea->steps()->count())->toBe(1);

});
