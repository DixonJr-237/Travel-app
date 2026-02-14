@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> 404 - Page Not Found
                    </h4>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-1 text-muted">404</h1>
                    <h3 class="text-muted">Oops! Page not found.</h3>
                    <p class="lead">
                        The page you're looking for doesn't exist or has been moved.
                    </p>
                    <a href="{{ route('welcome') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Go to Homepage
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
