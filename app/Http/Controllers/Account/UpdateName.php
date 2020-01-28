<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateName extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
        ]);
        if( $validator->fails())
        {
            return redirect()->back()->withErrors( $validator->errors() );
        }

        $user = Auth::user();
        $user->name = $request->input('name');
        try{
            $user->save();
        }
        catch( Exception $e ){ return redirect()->back()->withErrors( [$e->getMessage()]); }
        return redirect()->back()->withStatus('Your name has been updated!');
    }
}
