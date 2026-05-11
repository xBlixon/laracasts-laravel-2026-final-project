<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionsController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function destroy()
    {
        Auth::logout();
        return redirect('/')->with('success', 'You are now logged out!');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        if(!Auth::attempt($attributes)) {
            return back()
                ->withErrors(['password' => 'Wrong email or password.'])
                ->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', 'You are now logged in!');
    }
}
