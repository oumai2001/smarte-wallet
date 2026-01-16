<?php 
$active_page = 'dashboard';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="content">
    <h2><i class="fas fa-chart-line"></i> Tableau de Bord</h2>
    
    <p style="color: var(--text-secondary); margin-bottom: 2rem;">
        Bienvenue <?= htmlspecialchars($user_name) ?> ! Voici un aperçu de votre situation financière.
    </p>

    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Cartes de statistiques -->
    <div class="stats-grid">
        <!-- Total Revenus -->
        <div class="stat-card" style="border-left: 4px solid var(--success);">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Revenus</div>
                <div class="stat-value amount" style="color: var(--success);">
                    <?= number_format($total_income, 2, ',', ' ') ?> DH
                </div>
            </div>
        </div>

        <!-- Total Dépenses -->
        <div class="stat-card" style="border-left: 4px solid var(--danger);">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Dépenses</div>
                <div class="stat-value amount" style="color: var(--danger);">
                    <?= number_format($total_expense, 2, ',', ' ') ?> DH
                </div>
            </div>
        </div>

        <!-- Solde -->
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Solde Actuel</div>
                <div class="stat-value amount" style="color: <?= $balance >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                    <?= number_format($balance, 2, ',', ' ') ?> DH
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->   
    <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <a href="<?= BASE_URL ?>income" class="action-card" style="text-decoration: none; background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); transition: all 0.3s;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; border-radius: var(--radius); background: rgba(34, 197, 94, 0.1); display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 1.5rem;">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary);">Ajouter un Revenu</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">Enregistrer un nouveau revenu</div>
                </div>
            </div>
        </a>

        <a href="<?= BASE_URL ?>expense" class="action-card" style="text-decoration: none; background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); transition: all 0.3s;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; border-radius: var(--radius); background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 1.5rem;">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary);">Ajouter une Dépense</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">Enregistrer une nouvelle dépense</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Informations supplémentaires -->
    <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-main); border-radius: var(--radius); border-left: 4px solid var(--primary);">
        <h3 style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-info-circle"></i> Conseil Financier
        </h3>
        <p style="color: var(--text-secondary); line-height: 1.6;">
            <?php if ($balance >= 0): ?>
                Excellent ! Votre solde est positif. Continuez à surveiller vos dépenses et à épargner régulièrement.
            <?php else: ?>
                Attention : votre solde est négatif. Il est temps de revoir vos dépenses et d'augmenter vos revenus.
            <?php endif; ?>
        </p>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--bg-card);
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>