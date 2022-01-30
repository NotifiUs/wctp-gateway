<?php

namespace App\Http\Controllers\Messages;

use App\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowMessages extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request, $direction = null )
    {
        $filter = $direction;

        $distinctStatusList = Message::distinct('status')->get('status');
        $statusFilter = $request->get('status');

        if( ! is_null( $direction ) && $direction == 'inbound' ){
            $messages = Message::where( 'direction', 'inbound' )->orderBy('created_at', 'desc')->paginate(25);
        }
        elseif( ! is_null( $direction ) && $direction == 'outbound' ){
            $messages = Message::where( 'direction', 'outbound' )->orderBy('created_at', 'desc')->paginate(25);
        }
        else{
            $filter = null;
            $messages = Message::orderBy('created_at', 'desc')->paginate(25);
        }

        return view('messages.show')->with('messages', $messages )->with('filter', $filter );
    }
}
