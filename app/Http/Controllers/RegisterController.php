<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function create()
    {
        return view('user.auth.register');
    }

    public function store(RegisterRequest $request, CreateNewUser $creator)
    {
        $validated = $request->validated();

        $user = $creator->create($validated);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
