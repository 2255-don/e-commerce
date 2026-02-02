@php
$configData = Helper::appClasses();
$pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Jouan-Sugu - Plateforme E-commerce & Fintech')

@section('page-style')
<style>
  /* Modern Landing Page Styles */
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --fintech-blue: #2563eb;
    --fintech-purple: #7c3aed;
  }

  /* Navbar Moderne */
  .modern-navbar {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95) !important;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .nav-link {
    font-weight: 500;
    color: #64748b !important;
    transition: color 0.3s ease;
    position: relative;
  }

  .nav-link:hover {
    color: var(--fintech-purple) !important;
  }

  .nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -5px;
    left: 50%;
    background: var(--primary-gradient);
    transition: all 0.3s ease;
    transform: translateX(-50%);
  }

  .nav-link:hover::after {
    width: 100%;
  }

  .btn-auth {
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-login {
    color: var(--fintech-purple);
    border: 2px solid var(--fintech-purple);
    background: transparent;
  }

  .btn-login:hover {
    background: var(--fintech-purple);
    color: white;
  }

  .btn-register {
    background: var(--primary-gradient);
    color: white;
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }

  .btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
  }

  /* Hero Section */
  .hero-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
  }

  .hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: var(--primary-gradient);
    opacity: 0.1;
    border-radius: 0 0 0 100%;
  }

  .hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }

  .gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-subtitle {
    font-size: 1.25rem;
    color: #64748b;
    margin-bottom: 2.5rem;
    line-height: 1.8;
  }

  .hero-cta {
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-primary-gradient {
    background: var(--primary-gradient);
    border: none;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  }

  .btn-primary-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
  }

  .btn-outline-modern {
    border: 2px solid var(--fintech-purple);
    color: var(--fintech-purple);
    background: white;
  }

  .btn-outline-modern:hover {
    background: var(--fintech-purple);
    color: white;
    transform: translateY(-3px);
  }

  /* Features Section */
  .features-section {
    padding: 80px 0;
    background: white;
  }

  .section-title {
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1rem;
  }

  .section-subtitle {
    text-align: center;
    color: #64748b;
    font-size: 1.1rem;
    margin-bottom: 4rem;
  }

  .feature-card {
    border: none;
    border-radius: 20px;
    padding: 2rem;
    height: 100%;
    transition: all 0.4s ease;
    background: white;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.4s ease;
  }

  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  }

  .feature-card:hover::before {
    transform: scaleX(1);
  }

  .feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
  }

  .feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
  }

  .feature-icon i {
    font-size: 2.5rem;
    color: white;
  }

  .feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #1e293b;
  }

  .feature-description {
    color: #64748b;
    line-height: 1.7;
  }

  /* Products Preview Section */
  .products-section {
    padding: 80px 0;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
  }

  .product-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  }

  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  }

  .browse-products-btn {
    background: var(--primary-gradient);
    color: white;
    padding: 1rem 3rem;
    border-radius: 50px;
    font-weight: 600;
    border: none;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
  }

  .browse-products-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
  }

  .login-notice {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-left: 4px solid #f59e0b;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    display: inline-block;
    margin-top: 1rem;
  }

  .login-notice i {
    color: #f59e0b;
    margin-right: 0.5rem;
  }

  /* Stats Section */
  .stats-section {
    padding: 60px 0;
    background: var(--primary-gradient);
    color: white;
  }

  .stat-item {
    text-align: center;
  }

  .stat-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
  }

  .stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
  }

  /* Footer */
  .modern-footer {
    background: #1e293b;
    color: #94a3b8;
    padding: 60px 0 30px;
  }

  .footer-title {
    color: white;
    font-weight: 700;
    margin-bottom: 1.5rem;
  }

  .footer-link {
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.3s ease;
    display: block;
    margin-bottom: 0.8rem;
  }

  .footer-link:hover {
    color: white;
    padding-left: 5px;
  }

  .social-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    transition: all 0.3s ease;
  }

  .social-icon:hover {
    background: var(--primary-gradient);
    transform: translateY(-3px);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .hero-title {
      font-size: 2.5rem;
    }
    
    .hero-subtitle {
      font-size: 1rem;
    }
    
    .section-title {
      font-size: 2rem;
    }
  }

  /* Animations */
  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
  }

  .hero-image {
    animation: float 6s ease-in-out infinite;
  }
</style>
@endsection

@section('content')
<!-- Modern Navbar -->
<nav class="navbar navbar-expand-lg modern-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3" href="{{ url('/') }}">
      <span class="gradient-text">Jouan-Sugu</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item me-3">
          <a class="nav-link" href="{{ url('/') }}">Accueil</a>
        </li>
        <li class="nav-item me-3">
          <a class="nav-link" href="{{ route('marketplace.index') }}">Boutique</a>
        </li>
        <li class="nav-item me-3">
          <a class="nav-link" href="#features">Services</a>
        </li>
        <li class="nav-item me-3">
          <a class="nav-link" href="#contact">Contact</a>
        </li>
        
        @guest
          <li class="nav-item me-2">
            <a href="{{ route('login') }}" class="btn btn-auth btn-login">Connexion</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('register') }}" class="btn btn-auth btn-register">S'inscrire</a>
          </li>
        @else
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="btn btn-auth btn-register">
              <i class='bx bxs-dashboard'></i> Dashboard
            </a>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-5 mb-lg-0">
        <h1 class="hero-title">
          Votre Plateforme <br>
          <span class="gradient-text">E-commerce & Fintech</span>
        </h1>
        <p class="hero-subtitle">
          Achetez et vendez en toute sécurité avec notre plateforme intégrée. 
          Gérez vos transactions, votre portefeuille digital, et développez votre business en ligne.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          @auth
            <a href="{{ route('seller.dashboard') }}" class="btn hero-cta btn-primary-gradient">
              <i class='bx bx-store me-2'></i>Commencer à Vendre
            </a>
          @else
            <a href="{{ route('register') }}" class="btn hero-cta btn-primary-gradient">
              <i class='bx bx-store me-2'></i>Commencer à Vendre
            </a>
          @endauth
          <a href="{{ route('marketplace.index') }}" class="btn hero-cta btn-outline-modern">
            <i class='bx bx-shopping-bag me-2'></i>Parcourir les Produits
          </a>
        </div>
        @guest
          <div class="login-notice mt-4">
            <i class='bx bx-info-circle'></i>
            <small class="text-dark fw-semibold">Connectez-vous pour effectuer des achats et accéder à toutes les fonctionnalités</small>
          </div>
        @endguest
      </div>
      <div class="col-lg-6 text-center">
        <img src="{{ asset('assets/img/illustrations/girl-with-laptop-light.png') }}" 
             class="img-fluid hero-image" 
             alt="E-commerce Illustration"
             style="max-height: 500px;">
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-section" id="features">
  <div class="container">
    <h2 class="section-title">Pourquoi Choisir <span class="gradient-text">Jouan-Sugu</span> ?</h2>
    <p class="section-subtitle">Une plateforme complète alliant e-commerce et services financiers</p>
    
    <div class="row g-4">
      <!-- Feature 1: E-commerce -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-store-alt'></i>
          </div>
          <h3 class="feature-title">Créez Votre Boutique</h3>
          <p class="feature-description">
            Lancez votre boutique en ligne en quelques minutes. Interface intuitive et gestion simplifiée de vos produits.
          </p>
        </div>
      </div>

      <!-- Feature 2: Fintech - Wallet -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-wallet'></i>
          </div>
          <h3 class="feature-title">Portefeuille Digital</h3>
          <p class="feature-description">
            Gérez vos finances facilement avec notre portefeuille intégré. Rechargez et payez en toute sécurité.
          </p>
        </div>
      </div>

      <!-- Feature 3: E-commerce - Secure -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-shield-quarter'></i>
          </div>
          <h3 class="feature-title">Achats Sécurisés</h3>
          <p class="feature-description">
            Profitez d'une expérience d'achat sécurisée avec notre système de paiement crypté et fiable.
          </p>
        </div>
      </div>

      <!-- Feature 4: Fintech - Fast Payments -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-bitcoin'></i>
          </div>
          <h3 class="feature-title">Paiements Instantanés</h3>
          <p class="feature-description">
            Transactions rapides et transparentes. Recevez vos paiements instantanément dans votre portefeuille.
          </p>
        </div>
      </div>

      <!-- Feature 5: KYC -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-check-shield'></i>
          </div>
          <h3 class="feature-title">Vérification KYC</h3>
          <p class="feature-description">
            Système de vérification d'identité pour garantir la confiance et la sécurité de tous les utilisateurs.
          </p>
        </div>
      </div>

      <!-- Feature 6: Seller License -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-id-card'></i>
          </div>
          <h3 class="feature-title">Licence Vendeur</h3>
          <p class="feature-description">
            Obtenez votre licence de vendeur facilement et commencez à vendre vos produits en toute légalité.
          </p>
        </div>
      </div>

      <!-- Feature 7: Order Tracking -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-package'></i>
          </div>
          <h3 class="feature-title">Suivi de Commandes</h3>
          <p class="feature-description">
            Suivez vos commandes en temps réel, de la confirmation à la livraison finale.
          </p>
        </div>
      </div>

      <!-- Feature 8: Support -->
      <div class="col-lg-3 col-md-6">
        <div class="card feature-card">
          <div class="feature-icon">
            <i class='bx bx-support'></i>
          </div>
          <h3 class="feature-title">Support 24/7</h3>
          <p class="feature-description">
            Notre équipe est disponible pour vous accompagner à tout moment dans votre expérience.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Products Preview Section -->
<section class="products-section">
  <div class="container">
    <h2 class="section-title">Découvrez Nos <span class="gradient-text">Produits</span></h2>
    <p class="section-subtitle">Parcourez notre marketplace et trouvez ce dont vous avez besoin</p>
    
    <div class="text-center mt-5">
      <a href="{{ route('marketplace.index') }}" class="btn browse-products-btn">
        <i class='bx bx-shopping-bag me-2'></i>Explorer la Boutique
      </a>
      
      @guest
        <div class="d-flex justify-content-center">
          <div class="login-notice">
            <i class='bx bx-lock-alt'></i>
            <span class="text-dark">
              <strong>Note:</strong> Vous devez être connecté pour ajouter des produits au panier et effectuer des achats.
              <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: #f59e0b;">Se connecter</a>
            </span>
          </div>
        </div>
      @endguest
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-6 mb-4 mb-md-0">
        <div class="stat-item">
          <div class="stat-number">1000+</div>
          <div class="stat-label">Vendeurs Actifs</div>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-4 mb-md-0">
        <div class="stat-item">
          <div class="stat-number">5000+</div>
          <div class="stat-label">Produits Disponibles</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">10K+</div>
          <div class="stat-label">Transactions Réussies</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="stat-item">
          <div class="stat-number">99.9%</div>
          <div class="stat-label">Taux de Satisfaction</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modern Footer -->
<footer class="modern-footer" id="contact">
  <div class="container">
    <div class="row">
      <!-- About Column -->
      <div class="col-lg-4 col-md-6 mb-4">
        <h5 class="footer-title">
          <span class="gradient-text">Jouan-Sugu</span>
        </h5>
        <p class="mb-4">
          Votre plateforme de confiance combinant e-commerce et services financiers pour une expérience d'achat et de vente simplifiée.
        </p>
        <div>
          <a href="#" class="social-icon">
            <i class='bx bxl-facebook'></i>
          </a>
          <a href="#" class="social-icon">
            <i class='bx bxl-twitter'></i>
          </a>
          <a href="#" class="social-icon">
            <i class='bx bxl-instagram'></i>
          </a>
          <a href="#" class="social-icon">
            <i class='bx bxl-linkedin'></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="footer-title">Acheter</h6>
        <a href="{{ route('marketplace.index') }}" class="footer-link">Boutique</a>
        <a href="#" class="footer-link">Catégories</a>
        <a href="#" class="footer-link">Nouveautés</a>
        <a href="#" class="footer-link">Promotions</a>
      </div>

      <!-- Seller Links -->
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="footer-title">Vendre</h6>
        <a href="{{ route('seller.license') }}" class="footer-link">Devenir Vendeur</a>
        @auth
          <a href="{{ route('seller.dashboard') }}" class="footer-link">Dashboard Vendeur</a>
        @endauth
        <a href="#" class="footer-link">Guide Vendeur</a>
        <a href="#" class="footer-link">Tarifs</a>
      </div>

      <!-- Support Links -->
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="footer-title">Support</h6>
        <a href="#" class="footer-link">Centre d'Aide</a>
        <a href="#" class="footer-link">FAQ</a>
        <a href="#" class="footer-link">Contact</a>
        <a href="#" class="footer-link">Conditions</a>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="footer-title">Contact</h6>
        <p>
          <i class='bx bx-envelope me-2'></i>
          support@jouan-sugu.com
        </p>
        <p>
          <i class='bx bx-phone me-2'></i>
          +243 XX XXX XXXX
        </p>
        <p>
          <i class='bx bx-map me-2'></i>
          Kinshasa, RDC
        </p>
      </div>
    </div>

    <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 2rem 0;">
    
    <div class="row">
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <p class="mb-0">&copy; {{ date('Y') }} Jouan-Sugu. Tous droits réservés.</p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <a href="#" class="footer-link d-inline me-3">Confidentialité</a>
        <a href="#" class="footer-link d-inline me-3">Conditions</a>
        <a href="#" class="footer-link d-inline">Plan du Site</a>
      </div>
    </div>
  </div>
</footer>
@endsection