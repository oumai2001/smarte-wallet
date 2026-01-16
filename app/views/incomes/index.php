<?php 
$active_page = 'income';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="content">
    <h2><i class="fas fa-arrow-up"></i> Gestion des Revenus</h2>
    
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

    <form method="POST" action="<?= BASE_URL ?>income/add" style="background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-plus-circle"></i> Ajouter un Revenu</h3>
        
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" required>
        </div>
        
        <div class="form-group">
            <label>Montant (DH)</label>
            <input type="number" step="0.01" name="amount" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Catégorie</label>
            <select name="category_id">
                <option value="">-- Aucune --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Ajouter
        </button>
    </form>

    <!-- Filtres -->
    <form method="GET" action="<?= BASE_URL ?>income" class="filters">
        <select name="category" onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="month" name="month" value="<?= $month_filter ?? '' ?>" onchange="this.form.submit()">
        
        <?php if ($category_filter || $month_filter): ?>
            <a href="<?= BASE_URL ?>income" class="btn btn-small">
                <i class="fas fa-redo"></i> Réinitialiser
            </a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($incomes)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        Aucun revenu enregistré
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($incomes as $income): ?>
                    <tr>
                        <td><?= htmlspecialchars($income['description']) ?></td>
                        <td><?= htmlspecialchars($income['category_name'] ?? '-') ?></td>
                        <td style="color: var(--success); font-weight: bold;">
                            <?= number_format($income['amount'], 2, ',', ' ') ?> DH
                        </td>
                        <td><?= date('d/m/Y', strtotime($income['income_date'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>income/edit/<?= $income['id'] ?>" class="btn btn-small">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="<?= BASE_URL ?>income/delete/<?= $income['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('Confirmer la suppression ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>