<?php

/**
 * Model Expense
 * Gère les opérations liées aux dépenses
 */
class Expense extends Model
{
    protected $table = 'expenses';

    private $id;
    private $description;
    private $amount;
    private $expenseDate;
    private $categoryId;
    private $userId;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getId() { return $this->id; }
    public function getDescription() { return $this->description; }
    public function getAmount() { return $this->amount; }
    public function getExpenseDate() { return $this->expenseDate; }
    public function getCategoryId() { return $this->categoryId; }
    public function getUserId() { return $this->userId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setDescription($description) { $this->description = $description; }
    public function setAmount($amount) { $this->amount = $amount; }
    public function setExpenseDate($expenseDate) { $this->expenseDate = $expenseDate; }
    public function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    public function setUserId($userId) { $this->userId = $userId; }

    /**
     * Récupère toutes les dépenses d'un utilisateur
     */
    public function getAllByUser($userId, $categoryId = null, $month = null)
    {
        $sql = "SELECT e.*, c.name as category_name 
                FROM {$this->table} e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($categoryId) {
            $sql .= " AND e.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(e.expense_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }
        
        $sql .= " ORDER BY e.expense_date DESC, e.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une dépense par ID avec vérification utilisateur
     */
    public function getByIdAndUser($id, $userId)
    {
        $sql = "SELECT e.*, c.name as category_name 
                FROM {$this->table} e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.id = :id AND e.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle dépense
     */
    public function createExpense($data, $userId)
    {
        $expenseData = [
            'description' => htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8'),
            'amount' => floatval($data['amount']),
            'expense_date' => $data['date'],
            'category_id' => !empty($data['category_id']) ? intval($data['category_id']) : null,
            'user_id' => $userId
        ];

        // Validation
        if ($expenseData['amount'] <= 0) {
            return false;
        }

        if (!$this->validateDate($expenseData['expense_date'])) {
            return false;
        }

        return $this->create($expenseData);
    }

    /**
     * Met à jour une dépense
     */
    public function updateExpense($id, $data, $userId)
    {
        // Vérifier que la dépense appartient à l'utilisateur
        $expense = $this->getByIdAndUser($id, $userId);
        if (!$expense) {
            return false;
        }

        $updateData = [
            'description' => htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8'),
            'amount' => floatval($data['amount']),
            'expense_date' => $data['date'],
            'category_id' => !empty($data['category_id']) ? intval($data['category_id']) : null
        ];

        // Validation
        if ($updateData['amount'] <= 0) {
            return false;
        }

        if (!$this->validateDate($updateData['expense_date'])) {
            return false;
        }

        return $this->update($id, $updateData);
    }

    /**
     * Supprime une dépense avec vérification utilisateur
     */
    public function deleteExpense($id, $userId)
    {
        // Vérifier que la dépense appartient à l'utilisateur
        $expense = $this->getByIdAndUser($id, $userId);
        if (!$expense) {
            return false;
        }

        return $this->delete($id);
    }

    /**
     * Calcule le total des dépenses
     */
    public function getTotalByUser($userId, $month = null)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(expense_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les dépenses par catégorie
     */
    public function getByCategory($userId, $categoryId)
    {
        return $this->getAllByUser($userId, $categoryId);
    }

    /**
     * Valide une date
     */
    private function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Récupère les statistiques mensuelles
     */
    public function getMonthlyStats($userId, $year)
    {
        $sql = "SELECT 
                    DATE_FORMAT(expense_date, '%Y-%m') as month,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE user_id = :user_id 
                AND YEAR(expense_date) = :year
                GROUP BY month
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':year' => $year]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les dépenses par catégorie (statistiques)
     */
    public function getStatsByCategory($userId, $month = null)
    {
        $sql = "SELECT 
                    c.name as category_name,
                    c.id as category_id,
                    SUM(e.amount) as total,
                    COUNT(e.id) as count
                FROM {$this->table} e
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE e.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(e.expense_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }
        
        $sql .= " GROUP BY c.id, c.name ORDER BY total DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}