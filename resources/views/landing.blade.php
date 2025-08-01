@extends('layouts.app')

@section('title', __('messages.home'))

@push('styles')
<style>
    .main-content {
        /* Adds padding to the top and bottom, and to the sides on larger screens */
        padding: 2rem 0;
    }
    @media (min-width: 768px) {
        .main-content {
            padding: 3rem 2rem;
        }
    }

    /* Logo Section */
    .logo-section {
        text-align: center;
        padding: 2rem 0;
        background-color: #ffffff;
    }
    .app-logo {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        display: inline-block;
    }
    .app-title {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--dr-em-primary);
    }
    .app-subtitle {
        font-size: 1.3rem;
        color: #6c757d;
        max-width: 650px;
        margin: 0 auto;
    }

    /* Stats Section */
    .stats-section {
        background-color: #f8f9fa;
    }
    .stat-card {
        border: none;
        background-color: #ffffff;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .stat-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .stat-icon {
        font-size: 3rem;
        color: var(--dr-em-accent);
        margin-bottom: 1rem;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dr-em-primary);
    }
    .stat-label {
        font-size: 1.1rem;
        color: #6c757d;
    }

    /* Recent Pets Section */
    .pet-card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .pet-card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    /* Get Involved Section */
    .get-involved-section {
        background-color: var(--dr-em-primary);
        color: white;
    }
    .get-involved-section h2 {
        font-weight: 700;
    }
    .contact-info {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dr-em-accent);
        letter-spacing: 2px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container">
        <!-- Logo Section -->
        <div class="hero-section text-center">
    <div class="d-flex justify-content-center">
        <img src="{{ asset('images/img.jpg') }}" alt="UltraVet Logo" class="img-fluid" style="max-height: 180px;">
    </div>
    <h1 class="display-4 my-4">{{ __('messages.landing.title') }}</h1>
    <p class="lead">{{ __('messages.landing.subtitle') }}</p>
</div>

<!-- Stats Section -->
<div class="container stats-section my-5">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="stat-item">
                <h3 class="stat-number">{{ number_format($stats['total_pets']) }}+</h3>
                <p class="stat-label">{{ __('messages.landing.registered_pets') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-item">
                <h3 class="stat-number">10+</h3>
                <p class="stat-label">{{ __('messages.landing.partner_teams') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-item">
                <h3 class="stat-number">5+</h3>
                <p class="stat-label">{{ __('messages.landing.covered_governorates') }}</p>
            </div>
        </div>
    </div>
</div>

    <!-- Recent Pets Section -->
    <div id="recent-pets" class="bg-white py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5">{{ __('messages.recently_registered_animals') }}</h2>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
                @forelse ($recentPets as $pet)
                    <div class="col">
                        <div class="card pet-card h-100 shadow-sm">
                            <img src="{{ asset('storage/' . $pet->image_path) }}" class="card-img-top pet-card-img" alt="A stray pet">
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold">{{ $pet->breed_name ?? $pet->animal_type }}</h5>
                                <p class="card-text text-muted">{{ $pet->city_province ?? __('messages.location_not_specified') }}</p>
                                <a href="{{ route('stray-pets.public-view', $pet->uuid) }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('messages.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted fs-5">{{ __('messages.no_pets_to_display') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Get Involved Section -->
    <div class="get-involved-section py-5 text-center">
        <div class="container">
            <h2 class="display-4">{{ __('messages.get_involved') }}</h2>
            <p class="lead my-4">{{ __('messages.volunteer_contact') }}</p>
            <p class="contact-info">{{-- Fetch phone from settings later --}} 012-345-6789</p>
        </div>
    </div>
</div>
<style>
    .stat-item .stat-number {
        font-size: 3.5rem; /* Increased font size for the numbers */
        font-weight: bold;
    }
    .stat-item .stat-label {
        font-size: 1.5rem; /* Increased font size for the labels */
        color: #555;
    }
</style>
@endsection
