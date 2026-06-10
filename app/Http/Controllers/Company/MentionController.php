<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Mention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The header notification bell — @mentions raised against the logged-in user.
 */
class MentionController extends Controller
{
    public function markAllRead(Request $request): RedirectResponse
    {
        Mention::where('mentioned_user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
