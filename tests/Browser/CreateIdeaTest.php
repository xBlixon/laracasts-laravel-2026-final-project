<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

it('creates a new idea', function () {
    $this->actingAs($user = User::factory()->create());

    $title = 'Example title';
    $description = 'Example description';
    $link = 'https://example.com';
    $linkToBeRemoved = 'https://page.net';

    visit('/ideas')
        ->click('@create-idea-button')
        ->fill('title', $title)
        ->click('@button-status-completed')
        ->fill('description', $description)
        ->fill('@new-link', $link)
        ->click('@submit-new-link')

        ->fill('@new-link', $linkToBeRemoved)
        ->click('@submit-new-link')
        ->click('#remove-link-1')

        ->click('Create')
        ->assertPathIs('/ideas');

    expect($user->ideas()->count())->toBe(1)
        ->and($user->ideas()->first())->toMatchArray([
            'title' => $title,
            'status' => IdeaStatus::COMPLETED->value,
            'description' => $description,
            'links' => [$link],
        ]);

});
