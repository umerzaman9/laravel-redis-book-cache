<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Repositories\Interface\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthInterface $authRepo;

    public function __construct(AuthInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function sendOtp(RegistrationRequest $request)
    {
        try {
            return $this->authRepo->sendOtp($request);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Controller error in sendOtp'
            ]);
        }
    }

    public function showOtpForm()
    {
        try {
            return $this->authRepo->showOtpForm();
        } catch (\Exception $e) {
            return redirect()->route('register');
        }
    }

    public function resendOtp()
    {
        try {
            return $this->authRepo->resendOtp();
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'Controller error in resendOtp'
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            return $this->authRepo->verifyOtp($request);
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'Controller error in verifyOtp'
            ]);
        }
    }
}
