<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse;

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

        //event(new Registered($user));

        Auth::login($user);

        //return redirect()->route('verification.notice');

        return redirect()->route('user.attendance.create');
    }
}
