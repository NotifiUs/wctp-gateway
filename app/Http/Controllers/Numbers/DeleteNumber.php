<?php

namespace App\Http\Controllers\Numbers;

use App\Http\Controllers\Controller;
use App\Jobs\LogEvent;
use App\Models\Number;
use Exception;
use Illuminate\Support\Facades\Auth;

class DeleteNumber extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Number $number)
    {
        LogEvent::dispatch(
            "{$number->e164} released",
            get_class($this), 'info', json_encode($number->toArray()), Auth::user()->id ?? null
        );

        try {
            $number->delete();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([__('Unable to delete Phone Number')]);
        }

        $statusHtml = 'Phone Number released!';

        return redirect()->back()
            ->with('status', $statusHtml);
    }
}
