<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Smarte Wallet' ?> - Smarte Wallet</title>
    <meta name="description" content="Système de gestion financière professionnel">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <div class="header-container">
            <!-- Logo et Nom -->
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="brand">
                    <h1>Smarte Wallet</h1>
                    <p class="tagline">Gestion Intelligente de vos Finances</p>
                </div>
            </div>
            
            <!-- Navigation Desktop -->
            <nav class="desktop-nav">
                <a href="<?= BASE_URL ?>dashboard" class="nav-link <?= isset($active_page) && $active_page === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>income" class="nav-link <?= isset($active_page) && $active_page === 'income' ? 'active' : '' ?>">
                    <i class="fas fa-arrow-up"></i>
                    <span>Revenus</span>
                </a>
                <a href="<?= BASE_URL ?>expense" class="nav-link <?= isset($active_page) && $active_page === 'expense' ? 'active' : '' ?>">
                    <i class="fas fa-arrow-down"></i>
                    <span>Dépenses</span>
                </a>
            </nav>
            
            <!-- Actions utilisateur -->
            <div class="user-actions">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                        <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </div>
                <?php endif; ?>
                
                <button class="icon-btn" id="themeToggle" title="Changer le thème">
                    <i class="fas fa-moon"></i>
                </button>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>auth/logout" class="icon-btn" title="Déconnexion" style="text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php endif; ?>
                
                <button class="mobile-menu-toggle" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Navigation Mobile -->
    <nav class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-header">
            <h3>Menu</h3>
            <button class="close-mobile-nav" id="closeMobileNav">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-nav-links">
            <?php if (isset($_SESSION['user_name'])): ?>
                <div style="padding: 1rem; background: var(--bg-main); border-radius: var(--radius); margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--primary);"></i>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Utilisateur</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <a href="<?= BASE_URL ?>dashboard" class="<?= isset($active_page) && $active_page === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="<?= BASE_URL ?>income" class="<?= isset($active_page) && $active_page === 'income' ? 'active' : '' ?>">
                <i class="fas fa-arrow-up"></i> Revenus
            </a>
            <a href="<?= BASE_URL ?>expense" class="<?= isset($active_page) && $active_page === 'expense' ? 'active' : '' ?>">
                <i class="fas fa-arrow-down"></i> Dépenses
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>auth/logout" style="border-top: 1px solid var(--border); margin-top: 1rem; padding-top: 1rem; color: var(--danger);">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Overlay pour mobile -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Contenu Principal -->
    <main class="main-content">
        <div class="content-wrapper">