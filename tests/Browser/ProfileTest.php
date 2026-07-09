<?php

use App\Models\User;
use App\Notifications\EmailChanged;

it('requires authentication', function () {
    $this->get(route('profile.edit'))
        ->assertRedirect('/login');
});

it('edits a profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    visit(route('profile.edit'))
        ->assertValue('name', $user->name)
        ->fill('name', 'New name')
        ->assertValue('email', $user->email)
        ->fill('email', 'new@email.net')
        ->click('Update Account')
        ->assertSee('Profile updated!');

    expect($user->fresh())->toMatchArray([
        'name' => 'New name',
        'email' => 'new@email.net',
    ]);
});

it('notifies the original email on edit', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Notification::fake();

    $originalEmail = $user->email;

    visit(route('profile.edit'))
        ->assertValue('name', $user->name)
        ->fill('name', 'New name')
        ->assertValue('email', $user->email)
        ->fill('email', 'new@email.net')
        ->click('Update Account')
        ->assertSee('Profile updated!');

    Notification::assertSentOnDemand(EmailChanged::class, fn (EmailChanged $notification, $routes, $notifiable) => $notifiable->routes['mail'] === $originalEmail);
});
