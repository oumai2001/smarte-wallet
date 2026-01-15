<?php

/**
 * IncomeController
 * Gère les revenus
 */
class IncomeController extends Controller
{
    private $incomeModel;
    private $categoryModel;

    public function __construct()
    {
        $this->requireAuth();
        
        $this->incomeModel = $this->model('Income');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Liste des revenus
     */
    public function index()
    {
        $userId = $this->getUserId();
        
        // Filtres
        $categoryFilter = $_GET['category'] ?? null;
        $monthFilter = $_GET['month'] ?? null;

        // Récupérer les revenus
        $incomes = $this->incomeModel->getAllByUser($userId, $categoryFilter, $monthFilter);
        
        // Récupérer les catégories
        $categories = $this->categoryModel->getIncomeCategories();

        $data = [
            'title' => 'Gestion des Revenus',
            'incomes' => $incomes,
            'categories' => $categories,
            'category_filter' => $categoryFilter,
            'month_filter' => $monthFilter,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('incomes/index', $data);
    }

    /**
     * Ajouter un revenu
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            try {
                $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
            } catch (Exception $e) {
                $this->setFlash('error', 'Token de sécurité invalide');
                $this->redirect('income');
                return;
            }

            // Validation des champs
            if (!$this->validatePost(['description', 'amount', 'date'])) {
                $this->setFlash('error', 'Tous les champs obligatoires doivent être remplis');
                $this->redirect('income');
                return;
            }

            $data = [
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'category_id' => $_POST['category_id'] ?? null
            ];

            // Créer le revenu
            if ($this->incomeModel->createIncome($data, $this->getUserId())) {
                $this->setFlash('success', 'Revenu ajouté avec succès !');
            } else {
                $this->setFlash('error', 'Erreur lors de l\'ajout (vérifiez le montant et la date)');
            }

            $this->redirect('income');
        } else {
            $this->redirect('income');
        }
    }

    /**
     * Modifier un revenu
     */
    public function edit($id = null)
    {
        if (!$id) {
            $this->redirect('income');
            return;
        }

        $userId = $this->getUserId();
        
        // Récupérer le revenu
        $income = $this->incomeModel->getByIdAndUser($id, $userId);
        
        if (!$income) {
            $this->setFlash('error', 'Revenu introuvable');
            $this->redirect('income');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            try {
                $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
            } catch (Exception $e) {
                $this->setFlash('error', 'Token de sécurité invalide');
                $this->redirect('income/edit/' . $id);
                return;
            }

            // Validation des champs
            if (!$this->validatePost(['description', 'amount', 'date'])) {
                $this->setFlash('error', 'Tous les champs obligatoires doivent être remplis');
                $this->redirect('income/edit/' . $id);
                return;
            }

            $data = [
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'category_id' => $_POST['category_id'] ?? null
            ];

            // Mettre à jour le revenu
            if ($this->incomeModel->updateIncome($id, $data, $userId)) {
                $this->setFlash('success', 'Revenu modifié avec succès !');
                $this->redirect('income');
            } else {
                $this->setFlash('error', 'Erreur lors de la modification');
                $this->redirect('income/edit/' . $id);
            }
        } else {
            // Afficher le formulaire de modification
            $categories = $this->categoryModel->getIncomeCategories();

            $data = [
                'title' => 'Modifier le Revenu',
                'income' => $income,
                'categories' => $categories,
                'success' => $this->getFlash('success'),
                'error' => $this->getFlash('error'),
                'csrf_token' => $this->generateCsrfToken()
            ];

            $this->view('incomes/edit', $data);
        }
    }

    /**
     * Supprimer un revenu
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->redirect('income');
            return;
        }

        $userId = $this->getUserId();

        // Supprimer le revenu
        if ($this->incomeModel->deleteIncome($id, $userId)) {
            $this->setFlash('success', 'Revenu supprimé avec succès !');
        } else {
            $this->setFlash('error', 'Erreur lors de la suppression');
        }

        $this->redirect('income');
    }
}