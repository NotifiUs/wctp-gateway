<?php

namespace App\Http\Controllers\System;

use App\User;
use Exception;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EditUser extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, User $user)
    {
        $this->validate( $request, [
            'name' => 'required|string|min:3|max:255',
            'email' => "required|email|unique:users,email," . $user->id,
            'email_notifications' => 'nullable',
            'timezone' => 'required|timezone'
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->timezone = $request->input('timezone');
        $user->email_notifications = $request->input('email_notifications') ?? 0;

        try {
            $user->save();
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        LogEvent::dispatch(
            "User edited",
            get_class( $this ), 'info',json_encode( $user->toArray()) , Auth::user()->id ?? null
        );

       return redirect()->back()->with('status', 'User updated!');
    }
}
