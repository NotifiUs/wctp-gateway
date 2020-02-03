<?php

namespace App\Http\Controllers\System;

use App\User;
use Exception;
use App\EventLog;
use App\Jobs\LogEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteUser extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, User $user)
    {
        if( $user->id === Auth::user()->id )
        {
            return redirect()->back()->withErrors(['You can\'t delete your own account']);
        }

        try{
            $user_id = $user->id;
            $user->delete();
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors([$e->getMessage()] );
        }

        try{
            EventLog::where('user_id', $user_id)->update(['user_id' => null]);
        }
        catch( Exception $e )
        {
            return redirect()->back()->withErrors([$e->getMessage()] );
        }

        LogEvent::dispatch(
            "User deleted",
            get_class( $this ), 'info', json_encode(['deleted_user_id' => $user_id]), Auth::user()->id ?? null
        );

       return redirect()->back()->with('status', 'User deleted and log events updated!');
    }
}
