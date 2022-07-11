<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChangeEmail extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,'.Auth::user()->id,
            'email_notifications' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $user = Auth::user();

        if ($user->email != $request->input('email')) {
            $user->email_verified_at = null;
        }

        $user->email = $request->input('email');
        $user->email_notifications = $request->input('email_notifications') ?? 0;

        try {
            $user->save();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->back()->withStatus('Your email has been updated!');
    }
}
