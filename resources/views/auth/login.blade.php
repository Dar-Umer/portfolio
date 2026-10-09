@extends('layouts.public')

@section('title', 'Sign In | Client & Staff Portal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white d-inline-block rounded-circle p-3 mb-3">
                        <i class="fas fa-lock fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Portal Sign In</h3>
                    <p class="text-muted small">Access your projects, support tickets, quotations, and invoices.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', 'admin@theumer.store') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="password123" required>
                    </div>

                    <div class="mb-3 form-check d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        Sign In to Portal <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top text-center small text-muted">
                    Demo Accounts:<br>
                    <strong>Super Admin:</strong> <code>admin@theumer.store</code> (pwd: <code>password123</code>)<br>
                    <strong>Customer:</strong> <code>client@acme.com</code> (pwd: <code>password123</code>)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
