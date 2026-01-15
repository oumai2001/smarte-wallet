<?php

/**
 * DashboardController
 * Gère le tableau de bord et les statistiques
 */
class DashboardController extends Controller
{
    private $userModel;
    private $incomeModel;
    private $expenseModel;

    public function __construct()
    {
        $this->requireAuth();
        
        $this->userModel = $this->model('User');
        $this->incomeModel = $this->model('Income');
        $this->expenseModel = $this->model('Expense');
    }

    /**
     * Page principale du dashboard
     */
    public function index()
    {
        $userId = $this->getUserId();

        // Récupérer les statistiques globales
        $totalIncome = $this->incomeModel->getTotalByUser($userId);
        $totalExpense = $this->expenseModel->getTotalByUser($userId);
        $balance = $totalIncome - $totalExpense;

        // Récupérer les dernières transactions
        $recentIncomes = array_slice($this->incomeModel->getAllByUser($userId), 0, 5);
        $recentExpenses = array_slice($this->expenseModel->getAllByUser($userId), 0, 5);

        // Statistiques par catégorie
        $expensesByCategory = $this->expenseModel->getStatsByCategory($userId);

        $data = [
            'title' => 'Tableau de Bord',
            'user_name' => $this->getUserName(),
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'recent_incomes' => $recentIncomes,
            'recent_expenses' => $recentExpenses,
            'expenses_by_category' => $expensesByCategory,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ];

        $this->view('dashboard/index', $data);
    }

    /**
     * Statistiques mensuelles
     */
    public function monthly($year = null)
    {
        $userId = $this->getUserId();
        $year = $year ?? date('Y');

        $incomeStats = $this->incomeModel->getMonthlyStats($userId, $year);
        $expenseStats = $this->expenseModel->getMonthlyStats($userId, $year);

        $data = [
            'title' => 'Statistiques Mensuelles',
            'year' => $year,
            'income_stats' => $incomeStats,
            'expense_stats' => $expenseStats
        ];

        $this->view('dashboard/monthly', $data);
    }
}