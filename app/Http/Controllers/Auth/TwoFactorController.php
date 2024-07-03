<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    public function enable2fa()
    {
        // Check if 2FA is already enabled
        if (auth()->user()->enable_2fa) {
            return redirect()
                ->back()
                ->with('message', '2FA is already enabled.')
                ->with('message_type', 'warning');
        }

        // Generate a secret key for the user
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey();
		
        // Generate QR code for Google Authenticator
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            auth()->user()->username,
            $secret
        );

        User::where('id', auth()->user()->id)->update([
            'secret' => $secret,
        ]);
        return view('app.securities.generateQR', compact('QR_Image', 'secret'));
    }

	public function verify2fa(Request $request)
{
    $rules = [
        'secret' => 'required|min:6',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->with('message_type', 'warning');
    }

    $google2fa = app(Google2FA::class);
    $user = auth()->user();
    $inputSecret = $request->secret;
    $secret = $user->secret;

    // Debugging information
    Log::info('User ID: ' . $user->id);
    Log::info('Stored Secret (Base32): ' . $secret);
    Log::info('Input Secret: ' . $inputSecret);
    Log::info('Server Time: ' . now());

    // Verify the input secret with a wider time window
    $valid = $google2fa->verifyKey($secret, $inputSecret, 4); // Increased window to 4

    Log::info('OTP verification result: ' . ($valid ? 'valid' : 'invalid'));

    if ($valid) {
        // Update user record to enable 2FA
        User::where('id', $user->id)->update([
            'enable_2fa' => 1,
            'verify_2fa' => 1,
        ]);

        return redirect('dashboard')->with('message', '2FA enabled successfully.')->with('message_type', 'success');
    }

    return redirect()->back()->withErrors(['secret' => 'Incorrect secret code'])->with('message_type', 'danger');
}



}
