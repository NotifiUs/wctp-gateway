<?php

namespace App\Http\Controllers\Messages;

use App\Models\Message;
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

        $statusList = collect(Message::distinct('status')->get('status')->toArray() ?? [])->flatten();
        $statusFilter = $request->get('status') ?? null;

        if(  $direction === 'inbound' ){
            if($statusFilter) {
                $messages = Message::where( 'direction', 'inbound' )->where('status', $statusFilter)->orderBy('created_at', 'desc')->paginate(25);
            }
            else {
                $messages = Message::where( 'direction', 'inbound' )->orderBy('created_at', 'desc')->paginate(25);
            }

        }
        elseif(  $direction === 'outbound' ){
            if($statusFilter) {
                $messages = Message::where( 'direction', 'outbound' )->where('status', $statusFilter)->orderBy('created_at', 'desc')->paginate(25);
            }
            else {
                $messages = Message::where( 'direction', 'outbound' )->orderBy('created_at', 'desc')->paginate(25);
            }

        }
        else{

            $filter = null;

            if($statusFilter) {
                $messages = Message::orderBy('created_at', 'desc')->where('status', $statusFilter)->paginate(25);
            }
            else {
                $messages = Message::orderBy('created_at', 'desc')->paginate(25);
            }
        }

        return view('messages.show')->with('messages', $messages )->with('filter', $filter )->with('statusFilter', $statusFilter )->with('statusList', $statusList);
    }
}
