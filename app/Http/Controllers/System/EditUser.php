<?php

namespace App\Http\Controllers\System;

use App\Jobs\LogEvent;
use App\User;
use Exception;
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
            'timezone' => 'required|timezone'
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->timezone = $request->input('timezone');
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
