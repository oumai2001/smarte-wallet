<?php

/**
 * ExpenseController
 * Gère les dépenses
 */
class ExpenseController extends Controller
{
    private $expenseModel;
    private $categoryModel;

    public function __construct()
    {
        $this->requireAuth();
        
        $this->expenseModel = $this->model('Expense');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Liste des dépenses
     */
    public function index()
    {
        $userId = $this->getUserId();
        
        // Filtres
        $categoryFilter = $_GET['category'] ?? null;
        $monthFilter = $_GET['month'] ?? null;

        // Récupérer les dépenses
        $expenses = $this->expenseModel->getAllByUser($userId, $categoryFilter, $monthFilter);
        
        // Récupérer les catégories
        $categories = $this->categoryModel->getExpenseCategories();

        $data = [
            'title' => 'Gestion des Dépenses',
            'expenses' => $expenses,
            'categories' => $categories,
            'category_filter' => $categoryFilter,
            'month_filter' => $monthFilter,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('expenses/index', $data);
    }

    /**
     * Ajouter une dépense
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            try {
                $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
            } catch (Exception $e) {
                $this->setFlash('error', 'Token de sécurité invalide');
                $this->redirect('expense');
                return;
            }

            // Validation des champs
            if (!$this->validatePost(['description', 'amount', 'date'])) {
                $this->setFlash('error', 'Tous les champs obligatoires doivent être remplis');
                $this->redirect('expense');
                return;
            }

            $data = [
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'category_id' => $_POST['category_id'] ?? null
            ];

            // Créer la dépense
            if ($this->expenseModel->createExpense($data, $this->getUserId())) {
                $this->setFlash('success', 'Dépense ajoutée avec succès !');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'ajout (vérifiez le montant et la date)');
            }

            $this->redirect('expense');
        } else {
            $this->redirect('expense');
        }
    }

    /**
     * Modifier une dépense
     */
    public function edit($id = null)
    {
        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $userId = $this->getUserId();
        
        // Récupérer la dépense
        $expense = $this->expenseModel->getByIdAndUser($id, $userId);
        
        if (!$expense) {
            $this->setFlash('error', 'Dépense introuvable');
            $this->redirect('expense');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            try {
                $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
            } catch (Exception $e) {
                $this->setFlash('error', 'Token de sécurité invalide');
                $this->redirect('expense/edit/' . $id);
                return;
            }

            // Validation des champs
            if (!$this->validatePost(['description', 'amount', 'date'])) {
                $this->setFlash('error', 'Tous les champs obligatoires doivent être remplis');
                $this->redirect('expense/edit/' . $id);
                return;
            }

            $data = [
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'category_id' => $_POST['category_id'] ?? null
            ];

            // Mettre à jour la dépense
            if ($this->expenseModel->updateExpense($id, $data, $userId)) {
                $this->setFlash('success', 'Dépense modifiée avec succès !');
                $this->redirect('expense');
            } else {
                $this->setFlash('error', 'Erreur lors de la modification');
                $this->redirect('expense/edit/' . $id);
            }
        } else {
            // Afficher le formulaire de modification
            $categories = $this->categoryModel->getExpenseCategories();

            $data = [
                'title' => 'Modifier la Dépense',
                'expense' => $expense,
                'categories' => $categories,
                'success' => $this->getFlash('success'),
                'error' => $this->getFlash('error'),
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('expenses/edit', $data);
        }
    }

    /**
     * Supprimer une dépense
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $userId = $this->getUserId();

        // Supprimer la dépense
        if ($this->expenseModel->deleteExpense($id, $userId)) {
            $this->setFlash('success', 'Dépense supprimée avec succès !');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression');
        }

        $this->redirect('expense');
    }
}