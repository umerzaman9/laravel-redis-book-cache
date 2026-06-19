@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <h3 class="fw-bold">OTP Verification</h3>
                        <p class="text-muted mb-0">Enter the code sent to your email</p>
                    </div>


                    {{-- OTP Verify Form --}}
                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">OTP Code</label>
                            <input type="text" name="otp" class="form-control text-center fs-5"
                                placeholder="Enter 6-digit OTP" maxlength="6" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Verify OTP
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    {{-- Resend OTP --}}
                    <form method="POST" action="{{ route('otp.resend') }}">
                        @csrf

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-secondary">
                                Resend OTP
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection