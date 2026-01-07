@php
$configData = Helper::appClasses();
$pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Jouan-Sugu - Buy and Sell')

@section('page-style')
<style>
  /* Custom styles for landing page */
  .landing-hero {
    background-color: #f8f9fa;
    padding: 100px 0;
  }
  .feature-icon {
    font-size: 3rem;
    color: #7367f0;
    margin-bottom: 1rem;
  }
</style>
@endsection

@section('content')
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-none border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 text-primary" href="#">Jouan-Sugu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item me-3"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item me-3"><a class="nav-link" href="#">Products</a></li>
       
      </ul>
    </div>
  </div>
</nav>

<section class="landing-hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h1 class="display-4 fw-bold mb-4">The Marketplace for <span class="text-primary">Everyone</span></h1>
        <p class="lead mb-5 text-muted">Join Jouan-Sugu to buy the best products or sell your own. Secure, fast, and reliable.</p>
        <div class="d-flex gap-3">
          <a href="#" class="btn btn-primary btn-lg">Start Selling</a>
          <a href="#" class="btn btn-outline-secondary btn-lg">Shop Now</a>
        </div>
      </div>
      <div class="col-md-6 text-center mt-5 mt-md-0">
        <img src="{{ asset('assets/img/illustrations/girl-with-laptop-light.png') }}" class="img-fluid" alt="Hero Image">
      </div>
    </div>
  </div>
</section>

<!-- Stats/Features -->
<section class="py-5 bg-white">
  <div class="container text-center">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="bx bx-store feature-icon"></i>
                    <h3 class="h5">Create Your Store</h3>
                    <p class="text-muted">Set up your shop in minutes and reach thousands of customers.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="bx bx-check-shield feature-icon"></i>
                    <h3 class="h5">Secure Payments</h3>
                    <p class="text-muted">We ensure every transaction is safe and secure for both buyers and sellers.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="bx bx-rocket feature-icon"></i>
                    <h3 class="h5">Fast Delivery</h3>
                    <p class="text-muted">Trusted logistics partners to get your products delivered on time.</p>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>

<footer class="bg-light py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4">
        <h5 class="fw-bold mb-3">Jouan-Sugu</h5>
        <p class="text-muted">Your trusted partner in e-commerce.</p>
      </div>
      <div class="col-md-2 mb-4">
        <h6 class="fw-bold mb-3">Shop</h6>
        <ul class="list-unstyled">
            <li><a href="#" class="text-muted text-decoration-none">Electronics</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Fashion</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Home</a></li>
        </ul>
      </div>
      <div class="col-md-2 mb-4">
        <h6 class="fw-bold mb-3">Sell</h6>
        <ul class="list-unstyled">
            <li><a href="#" class="text-muted text-decoration-none">Start Selling</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Seller Handbook</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-4">
        <h6 class="fw-bold mb-3">Contact</h6>
        <p class="text-muted">support@jouan-sugu.com</p>
      </div>
    </div>
    <div class="text-center pt-4 border-top">
      <p class="text-muted mb-0">&copy; {{ date('Y') }} Jouan-Sugu. All rights reserved.</p>
    </div>
  </div>
</footer>
@endsection