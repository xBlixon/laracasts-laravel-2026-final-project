<?php

use App\IdeaStatus;
use App\Models\User;

it('creates a new idea', function () {
    $this->actingAs($user = User::factory()->create());

    $title = 'Example title';
    $description = 'Example description';
    $link = 'https://example.com';
    $linkToBeRemoved = 'https://page.net';
    $stepDescription = 'To do something.';

    visit('/ideas')
        ->click('@create-idea-button')
        ->fill('title', $title)
        ->click('@button-status-completed')
        ->fill('description', $description)
        ->fill('@new-link', $link)
        ->click('@submit-new-link')

        ->fill('@new-step', $stepDescription)
        ->click('@submit-new-step')

        ->fill('@new-link', $linkToBeRemoved)
        ->click('@submit-new-link')
        ->click('#remove-link-1')

        ->click('Create')
        ->assertPathIs('/ideas');

    expect($idea = $user->ideas()->first())->toMatchArray([
        'title' => $title,
        'status' => IdeaStatus::COMPLETED->value,
        'description' => $description,
        'links' => [$link],
    ])
        ->and($idea->steps()->first())->toMatchArray([
            'description' => $stepDescription,
        ]);

});
