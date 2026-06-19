<?php

namespace App\Repositories\Interface;

interface AuthInterface
{
    public function sendOtp($request);

    public function showOtpForm();

    public function resendOtp();

    public function verifyOtp($request);
}
