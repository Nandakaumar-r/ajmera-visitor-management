@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Registration Successful</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    <h3>Thank You for Registering!</h3>
                    <p class="lead">Your registration has been submitted successfully.</p>
                    <p>Please check your email to verify your account and complete your profile.</p>
                    <p>Once your profile is complete, our team will review your information and approve your account.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
