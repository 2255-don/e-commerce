@php
$configData = Helper::appClasses();
$pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Jouan-Sugu - Votre Marketplace E-commerce & Wallet Digital')

@section('page-style')
<!-- Boxicons -->
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  :root {
    --brand-gold: #B8860B;
    --brand-gold-light: #D4AF37;
    --brand-grey: #808080;
    --brand-grey-dark: #505050;
    --gradient-gold: linear-gradient(135deg, #B8860B 0%, #D4AF37 100%);
    --gradient-grey: linear-gradient(135deg, #505050 0%, #808080 100%);
  }

  body {
    overflow-x: hidden;
  }

  /* ==================== NAVBAR ==================== */
  .vitrine-navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 2px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .vitrine-navbar.scrolled {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 4px 40px rgba(0, 0, 0, 0.12);
  }

  .navbar-brand {
    font-size: 1.75rem;
    font-weight: 800;
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .nav-link {
    font-weight: 500;
    color: #334155 !important;
    padding: 0.75rem 1.25rem !important;
    position: relative;
    transition: color 0.3s ease;
  }

  .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: var(--gradient-gold);
    transition: width 0.3s ease;
    border-radius: 2px;
  }

  .nav-link:hover::after,
  .nav-link.active::after {
    width: 60%;
  }

  .btn-nav {
    padding: 0.65rem 1.75rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }

  .btn-login {
    color: var(--brand-gold);
    border: 2px solid var(--brand-gold);
    background: transparent;
  }

  .btn-login:hover {
    background: var(--brand-gold);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(184, 134, 11, 0.3);
  }

  .btn-register {
    background: var(--gradient-gold);
    color: white;
    border: none;
    box-shadow: 0 6px 20px rgba(184, 134, 11, 0.35);
  }

  .btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(184, 134, 11, 0.45);
  }

  /* ==================== HERO SECTION ==================== */
  .hero-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    position: relative;
    overflow: hidden;
    padding-top: 100px;
  }

  .hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 80%;
    height: 150%;
    background: radial-gradient(circle, rgba(184, 134, 11, 0.08) 0%, transparent 70%);
    animation: float 20s ease-in-out infinite;
  }

  @keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-30px, 30px) rotate(5deg); }
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero-badge {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    background: linear-gradient(135deg, rgba(184, 134, 11, 0.1) 0%, rgba(212, 175, 55, 0.15) 100%);
    border: 1px solid rgba(184, 134, 11, 0.3);
    border-radius: 50px;
    color: var(--brand-gold);
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    animation: slideInDown 0.8s ease;
  }

  .hero-title {
    font-size: 4rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    background: var(--gradient-grey);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: slideInLeft 0.8s ease 0.2s backwards;
  }

  .hero-title .highlight {
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-description {
    font-size: 1.25rem;
    color: #64748b;
    margin-bottom: 2.5rem;
    max-width: 600px;
    line-height: 1.8;
    animation: slideInLeft 0.8s ease 0.4s backwards;
  }

  .hero-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    animation: slideInUp 0.8s ease 0.6s backwards;
  }

  .btn-hero {
    padding: 1rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
  }

  .btn-primary-hero {
    background: var(--gradient-gold);
    color: white;
    border: none;
    box-shadow: 0 8px 25px rgba(184, 134, 11, 0.4);
  }

  .btn-primary-hero:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(184, 134, 11, 0.5);
  }

  .btn-secondary-hero {
    background: white;
    color: var(--brand-grey-dark);
    border: 2px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  }

  .btn-secondary-hero:hover {
    border-color: var(--brand-gold);
    color: var(--brand-gold);
    transform: translateY(-3px);
  }

  .hero-stats {
    display: flex;
    gap: 3rem;
    margin-top: 3rem;
    animation: slideInUp 0.8s ease 0.8s backwards;
  }

  .stat-item {
    text-align: center;
  }

  .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .stat-label {
    color: #64748b;
    font-size: 0.95rem;
    margin-top: 0.5rem;
  }

  .hero-image {
    position: relative;
    animation: slideInRight 1s ease 0.4s backwards;
  }

  .hero-image img {
    max-width: 100%;
    filter: drop-shadow(0 20px 60px rgba(0, 0, 0, 0.15));
    animation: levitate 3s ease-in-out infinite;
  }

  @keyframes levitate {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
  }

  /* ==================== FEATURES SECTION ==================== */
  .features-section {
    padding: 100px 0;
    background: white;
  }

  .section-badge {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    background: linear-gradient(135deg, rgba(184, 134, 11, 0.1) 0%, rgba(212, 175, 55, 0.15) 100%);
    border: 1px solid rgba(184, 134, 11, 0.3);
    border-radius: 50px;
    color: var(--brand-gold);
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 1rem;
  }

  .section-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 1rem;
  }

  .section-description {
    font-size: 1.15rem;
    color: #64748b;
    max-width: 700px;
    margin: 0 auto 4rem;
  }

  .feature-card {
    padding: 2.5rem;
    border-radius: 24px;
    background: white;
    border: 1px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--gradient-gold);
    opacity: 0;
    transition: opacity 0.4s ease;
  }

  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
    border-color: transparent;
  }

  .feature-card:hover::before {
    opacity: 0.05;
  }

  .feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 20px;
    background: var(--gradient-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 1;
  }

  .feature-icon i {
    font-size: 2rem;
    color: white;
  }

  .feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
  }

  .feature-description {
    color: #64748b;
    line-height: 1.7;
    position: relative;
    z-index: 1;
  }

  /* ==================== HOW IT WORKS ==================== */
  .how-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  }

  .step-card {
    text-align: center;
    padding: 2rem;
    position: relative;
  }

  .step-number {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--gradient-gold);
    color: white;
    font-size: 2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 10px 30px rgba(184, 134, 11, 0.3);
    position: relative;
  }

  .step-card::after {
    content: '→';
    position: absolute;
    top: 50px;
    right: -30px;
    font-size: 3rem;
    color: var(--brand-gold-light);
    opacity: 0.3;
  }

  .step-card:last-child::after {
    display: none;
  }

  .step-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
  }

  .step-description {
    color: #64748b;
    line-height: 1.6;
  }

  /* ==================== CTA SECTION ==================== */
  .cta-section {
    padding: 100px 0;
    background: var(--gradient-gold);
    position: relative;
    overflow: hidden;
  }

  .cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 30s linear infinite;
  }

  @keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .cta-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
  }

  .cta-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
  }

  .cta-description {
    font-size: 1.25rem;
    margin-bottom: 2.5rem;
    opacity: 0.95;
  }

  .btn-cta {
    padding: 1.25rem 3rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.15rem;
    background: white;
    color: var(--brand-gold);
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }

  .btn-cta:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
  }

  /* ==================== FOOTER ==================== */
  .footer {
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
    display: block;
    margin-bottom: 0.75rem;
    transition: color 0.3s ease;
  }

  .footer-link:hover {
    color: var(--brand-gold-light);
  }

  .social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
  }

  .social-link {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.3s ease;
  }

  .social-link:hover {
    background: var(--gradient-gold);
    transform: translateY(-3px);
  }

  /* ==================== ANIMATIONS ==================== */
  @keyframes slideInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideInLeft {
    from {
      opacity: 0;
      transform: translateX(-50px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(50px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes slideInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ==================== RESPONSIVE ==================== */
  @media (max-width: 768px) {
    .hero-title {
      font-size: 2.5rem;
    }

    .hero-stats {
      flex-direction: column;
      gap: 1.5rem;
    }

    .step-card::after {
      display: none;
    }

    .cta-title {
      font-size: 2rem;
    }
  }
</style>
@endsection

@section('content')

<!-- ==================== NAVBAR ==================== -->
<nav class="vitrine-navbar">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">
      <a href="/" class="navbar-brand">
        <i class='bx bxs-wallet' style="font-size: 1.5rem; margin-right: 0.5rem;"></i>
        Jouan-Sugu
      </a>

      <div class="d-none d-md-flex align-items-center gap-1">
        <a href="#features" class="nav-link">Fonctionnalités</a>
        <a href="#how-it-works" class="nav-link">Comment ça marche</a>
        <a href="{{ route('marketplace.index') }}" class="nav-link">Marketplace</a>
        <a href="#footer" class="nav-link">Contact</a>
      </div>

      <div class="d-flex align-items-center gap-2">
        @guest
          <a href="{{ route('login') }}" class="btn btn-nav btn-login">Connexion</a>
          <a href="{{ route('register') }}" class="btn btn-nav btn-register">Inscription</a>
        @else
          <a href="{{ route('dashboard') }}" class="btn btn-nav btn-register">
            <i class='bx bxs-dashboard'></i>
            Dashboard
          </a>
        @endguest
      </div>
    </div>
  </div>
</nav>

<!-- ==================== HERO SECTION ==================== -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 hero-content">
        <span class="hero-badge">
          <i class='bx bxs-zap'></i>
          Plateforme E-commerce & Fintech
        </span>

        <h1 class="hero-title">
          Achetez, Vendez et <span class="highlight">Gérez vos Finances</span> en un Seul Endroit
        </h1>

        <p class="hero-description">
          Découvrez Jouan-Sugu, la plateforme qui combine marketplace moderne et portefeuille digital pour une expérience d'achat et de vente sans friction.
        </p>

        <div class="hero-buttons">
          <a href="{{ route('register') }}" class="btn btn-hero btn-primary-hero">
            Commencer Gratuitement
            <i class='bx bx-right-arrow-alt' style="font-size: 1.5rem;"></i>
          </a>
          <a href="{{ route('marketplace.index') }}" class="btn btn-hero btn-secondary-hero">
            <i class='bx bxs-store'></i>
            Explorer la Boutique
          </a>
        </div>

        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-number">1000+</div>
            <div class="stat-label">Produits</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Vendeurs Actifs</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">5000+</div>
            <div class="stat-label">Utilisateurs</div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 hero-image d-none d-lg-block">
        <img src="https://illustrations.popsy.co/amber/online-shopping.svg" alt="Shopping Illustration">
      </div>
    </div>
  </div>
</section>

<!-- ==================== FEATURES SECTION ==================== -->
<section class="features-section" id="features">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-badge">Pourquoi Jouan-Sugu</span>
      <h2 class="section-title">Une Plateforme, Multiples Possibilités</h2>
      <p class="section-description">
        Découvrez comment Jouan-Sugu révolutionne votre expérience d'achat en ligne avec des fonctionnalités innovantes
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-shopping-bag'></i>
          </div>
          <h3 class="feature-title">Marketplace Moderne</h3>
          <p class="feature-description">
            Explorez des milliers de produits de qualité, de vendeurs vérifiés. Recherche intelligente et filtres avancés pour trouver exactement ce que vous cherchez.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-wallet'></i>
          </div>
          <h3 class="feature-title">Portefeuille Digital</h3>
          <p class="feature-description">
            Gérez vos finances en toute sécurité avec votre wallet intégré. Rechargez facilement et payez vos achats en un clic sans saisir vos coordonnées bancaires.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-store'></i>
          </div>
          <h3 class="feature-title">Devenez Vendeur</h3>
          <p class="feature-description">
            Lancez votre boutique en ligne en quelques minutes. Ajoutez vos produits, gérez votre stock et développez votre activité avec nos outils professionnels.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-lock-alt'></i>
          </div>
          <h3 class="feature-title">Sécurisé & Vérifié</h3>
          <p class="feature-description">
            KYC obligatoire pour tous les vendeurs. Vos transactions sont sécurisées et vos données protégées avec les dernières technologies de cryptage.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-zap'></i>
          </div>
          <h3 class="feature-title">Paiements Rapides</h3>
          <p class="feature-description">
            Transactions instantanées avec mobile money ou wallet. Achetez maintenant, recevez vos produits rapidement et confirmez la livraison en un clic.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon">
            <i class='bx bxs-support'></i>
          </div>
          <h3 class="feature-title">Support 24/7</h3>
          <p class="feature-description">
            Notre équipe est disponible pour vous aider à tout moment. Questions, problèmes ou conseils, nous sommes là pour vous accompagner dans votre expérience.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== HOW IT WORKS ==================== -->
<section class="how-section" id="how-it-works">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-badge">Processus Simple</span>
      <h2 class="section-title">Comment Ça Marche</h2>
      <p class="section-description">
        En seulement 3 étapes, commencez à acheter ou vendre sur Jouan-Sugu
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-number">1</div>
          <h3 class="step-title">Inscrivez-vous Gratuitement</h3>
          <p class="step-description">
            Créez votre compte en quelques secondes. Aucune carte bancaire requise. Recevez instantanément votre wallet digital.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="step-card">
          <div class="step-number">2</div>
          <h3 class="step-title">Rechargez Votre Wallet</h3>
          <p class="step-description">
            Ajoutez des fonds à votre portefeuille via mobile money (Airtel, Vodacom, etc.) de manière sécurisée et instantanée.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="step-card">
          <div class="step-number">3</div>
          <h3 class="step-title">Achetez ou Vendez</h3>
          <p class="step-description">
            Explorez la marketplace, ajoutez au panier et payez en un clic. Ou activez votre licence vendeur et commencez à vendre vos produits.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== CTA SECTION ==================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content">
      <h2 class="cta-title">Prêt à Commencer Votre Aventure ?</h2>
      <p class="cta-description">
        Rejoignez des milliers d'utilisateurs qui font confiance à Jouan-Sugu pour leurs achats et leurs ventes en ligne.
      </p>
      <a href="{{ route('register') }}" class="btn btn-cta">
        Créer Mon Compte Gratuitement
        <i class='bx bx-right-arrow-alt' style="font-size: 1.3rem; margin-left: 0.5rem;"></i>
      </a>
    </div>
  </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="footer" id="footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 mb-4">
        <h3 class="footer-title" style="background: var(--gradient-gold); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
          <i class='bx bxs-wallet' style="font-size: 1.5rem;"></i>
          Jouan-Sugu
        </h3>
        <p class="mb-3">
          La plateforme qui combine e-commerce et fintech pour une expérience d'achat unique en RDC.
        </p>
        <div class="social-links">
          <a href="#" class="social-link"><i class='bx bxl-facebook'></i></a>
          <a href="#" class="social-link"><i class='bx bxl-twitter'></i></a>
          <a href="#" class="social-link"><i class='bx bxl-instagram'></i></a>
          <a href="#" class="social-link"><i class='bx bxl-linkedin'></i></a>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 mb-4">
        <h5 class="footer-title">Plateforme</h5>
        <a href="{{ route('marketplace.index') }}" class="footer-link">Marketplace</a>
        <a href="{{ route('register') }}" class="footer-link">Devenir Vendeur</a>
        <a href="#features" class="footer-link">Fonctionnalités</a>
        <a href="#how-it-works" class="footer-link">Comment ça marche</a>
      </div>

      <div class="col-lg-2 col-md-4 mb-4">
        <h5 class="footer-title">Support</h5>
        <a href="#" class="footer-link">Centre d'aide</a>
        <a href="#" class="footer-link">FAQ</a>
        <a href="#" class="footer-link">Contact</a>
        <a href="#" class="footer-link">Statut</a>
      </div>

      <div class="col-lg-2 col-md-4 mb-4">
        <h5 class="footer-title">Légal</h5>
        <a href="#" class="footer-link">Conditions d'utilisation</a>
        <a href="#" class="footer-link">Politique de confidentialité</a>
        <a href="#" class="footer-link">CGV</a>
        <a href="#" class="footer-link">Mentions légales</a>
      </div>

      <div class="col-lg-2 col-md-4 mb-4">
        <h5 class="footer-title">Entreprise</h5>
        <a href="#" class="footer-link">À propos</a>
        <a href="#" class="footer-link">Blog</a>
        <a href="#" class="footer-link">Carrières</a>
        <a href="#" class="footer-link">Presse</a>
      </div>
    </div>

    <div class="text-center pt-4 mt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
      <p class="mb-0">&copy; {{ date('Y') }} Jouan-Sugu. Tous droits réservés. Fait avec <i class='bx bxs-heart' style="color: var(--brand-gold);"></i> en RDC</p>
    </div>
  </div>
</footer>

@endsection

@section('page-script')
<script>
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.vitrine-navbar');
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Add active class to nav links on scroll
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;
      if (scrollY >= (sectionTop - 200)) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  });
</script>
@endsection