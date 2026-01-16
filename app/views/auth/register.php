<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Smarte Wallet</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div style="background: var(--bg-card); padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); width: 100%; max-width: 450px;">
            
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: var(--radius); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; color: white; margin-bottom: 1rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Créer un compte</h1>
                <p style="color: var(--text-secondary);">Rejoignez Smarte Wallet</p>
            </div>

            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>auth/register">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nom Complet</label>
                    <input type="text" name="full_name" placeholder="Ex: Ahmed Bennani" required value="<?= $_POST['full_name'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="votre@email.com" required value="<?= $_POST['email'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="password" placeholder="Minimum 6 caractères" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirmer mot de passe</label>
                    <input type="password" name="confirm_password" placeholder="Retapez votre mot de passe" required>
                </div>

                <button type="submit" class="btn" style="width: 100%; justify-content: center; margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <p style="color: var(--text-secondary);">
                    Vous avez déjà un compte ? 
                    <a href="<?= BASE_URL ?>auth/login" style="color: var(--primary); font-weight: 500; text-decoration: none;">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Générer un token CSRF si nécessaire
        <?php if (!isset($_SESSION['csrf_token'])): ?>
            <?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
        <?php endif; ?>
    </script>
</body>
</html>