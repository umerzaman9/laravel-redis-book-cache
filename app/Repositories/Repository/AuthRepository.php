<?php

namespace App\Repositories\Repository;

use App\Mail\OtpMail;
use App\Models\User;
use App\Repositories\Interface\AuthInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class AuthRepository implements AuthInterface
{
    public function sendOtp($request)
    {
        try {

            $otp = random_int(100000, 999999);

            Redis::setex(
                'register:' . $request->email,
                300,
                json_encode([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'otp' => $otp,
                ])
            );

            Redis::setex('otp_cooldown:' . $request->email, 60, 1);

            Mail::to($request->email)->send(new OtpMail($otp));

            session([
                'registration_email' => $request->email
            ]);

            return redirect()->route('otp.form');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Something went wrong while sending OTP.'
            ]);
        }
    }

    public function showOtpForm()
    {
        try {
            return view('auth.verify-otp');
        } catch (\Exception $e) {
            return redirect()->route('register');
        }
    }

    public function resendOtp()
    {
        try {
            $email = session('registration_email');

            if (!$email) {
                return redirect()->route('register');
            }

            if (Redis::exists('otp_cooldown:' . $email)) {

                $seconds = Redis::ttl('otp_cooldown:' . $email);

                return back()->withErrors([
                    'otp' => "Please wait {$seconds} seconds before requesting another OTP."
                ]);
            }

            $data = Redis::get('register:' . $email);

            if (!$data) {
                return redirect()->route('register')
                    ->withErrors([
                        'otp' => 'Registration session expired.'
                    ]);
            }

            $data = json_decode($data, true);

            $newOtp = random_int(100000, 999999);
            $data['otp'] = $newOtp;

            Redis::setex('register:' . $email, 300, json_encode($data));

            Redis::setex('otp_cooldown:' . $email, 60, 1);

            Mail::to($email)->send(new OtpMail($newOtp));

            return back()->with('success', 'OTP resent successfully.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'Failed to resend OTP.'
            ]);
        }
    }

    public function verifyOtp($request)
    {
        try {
            $request->validate([
                'otp' => ['required']
            ]);

            $email = session('registration_email');

            if (!$email) {
                return redirect()->route('register');
            }

            $data = Redis::get('register:' . $email);

            if (!$data) {
                return back()->withErrors([
                    'otp' => 'OTP expired.'
                ]);
            }

            $data = json_decode($data, true);

            if ($data['otp'] != $request->otp) {
                return back()->withErrors([
                    'otp' => 'Invalid OTP.'
                ]);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Redis::del('register:' . $email);

            Auth::login($user);

            return redirect('/');
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'Verification failed.'
            ]);
        }
    }
}
