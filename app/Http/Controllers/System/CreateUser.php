<?php

namespace App\Http\Controllers\System;

use App\Jobs\LogEvent;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Mail\SendWelcomeEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class CreateUser extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3|max:255',
            'email' => "required|email|unique:users,email",
            'email_notifications' => 'nullable',
            'timezone' => 'required|timezone'
        ]);

        try {
            $password = bin2hex(random_bytes(12));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->timezone = $request->input('timezone');
        $user->email_notifications = $request->input('email_notifications') ?? 0;

        $user->password = Hash::make($password);

        try {
            $user->save();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        Mail::to($user->email)->send(new SendWelcomeEmail($user->email,$password));

        LogEvent::dispatch(
            "New user created",
            get_class( $this ), 'info',json_encode( $user->toArray()) , Auth::user()->id ?? null
        );

        return redirect()->back()->with('status', "User created and welcome email sent.");
    }
}
