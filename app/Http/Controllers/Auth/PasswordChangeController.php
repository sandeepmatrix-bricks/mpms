<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ResolvesLanding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    use ResolvesLanding;

    public function show(): View|RedirectResponse
    {
        // Nothing to do here if the user already set their own password.
        if (! Auth::user()->must_change_password) {
            return redirect($this->landingRoute(Auth::user()) ?? route('admin.login'));
        }

        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();
        $user->update([
            'password' => $data['password'],
            'must_change_password' => false,
        ]);

        // Land them wherever their account belongs (platform vs company portal).
        return redirect($this->landingRoute($user) ?? route('admin.login'))
            ->with('sweetalert', [
                'icon' => 'success',
                'title' => 'Password updated',
                'text' => 'Your new password is set. Welcome aboard!',
            ]);
    }
}
