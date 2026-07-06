<?php

use Illuminate\Support\Facades\Auth;

it('registers a user', function () {
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', 'foo@bar.com')
        ->fill('password', 'password123')
        ->click('Create Account')
        ->assertPathIs('/');

    $this->assertAuthenticated();

    expect(Auth::user())->toMatchArray([
        'name' => 'John Doe',
        'email' => 'foo@bar.com',
    ]);
});

it('requires a valid email', function () {
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', 'foobar')
        ->fill('password', 'password123')
        ->click('Create Account')
        ->assertPathIs('/register');
});
