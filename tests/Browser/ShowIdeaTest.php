<?php

use App\Models\Idea;
use App\Models\User;

it('requires authentication', function () {
    $idea = Idea::factory()->create();
    $this->get(route('idea.show', $idea))
        ->assertRedirectToRoute('login');
});

it('denies access to ideas of other users', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $idea = Idea::factory()->create();
    $this->get(route('idea.show', $idea))
        ->assertForbidden();
});
