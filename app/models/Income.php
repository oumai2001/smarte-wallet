<?php

/**
 * Model Income
 * Gère les opérations liées aux revenus
 */
class Income extends Model
{
    protected $table = 'incomes';

    private $id;
    private $description;
    private $amount;
    private $incomeDate;
    private $categoryId;
    private $userId;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getId() { return $this->id; }
    public function getDescription() { return $this->description; }
    public function getAmount() { return $this->amount; }
    public function getIncomeDate() { return $this->incomeDate; }
    public function getCategoryId() { return $this->categoryId; }
    public function getUserId() { return $this->userId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setDescription($description) { $this->description = $description; }
    public function setAmount($amount) { $this->amount = $amount; }
    public function setIncomeDate($incomeDate) { $this->incomeDate = $incomeDate; }
    public function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    public function setUserId($userId) { $this->userId = $userId; }

    /**
     * Récupère tous les revenus d'un utilisateur
     */
    public function getAllByUser($userId, $categoryId = null, $month = null)
    {
        $conditions = ['user_id' => $userId];
        
        if ($categoryId) {
            $conditions['category_id'] = $categoryId;
        }
        
        $sql = "SELECT i.*, c.name as category_name 
                FROM {$this->table} i 
                LEFT JOIN categories c ON i.category_id = c.id 
                WHERE i.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($categoryId) {
            $sql .= " AND i.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(i.income_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }
        
        $sql .= " ORDER BY i.income_date DESC, i.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un revenu par ID avec vérification utilisateur
     */
    public function getByIdAndUser($id, $userId)
    {
        $sql = "SELECT i.*, c.name as category_name 
                FROM {$this->table} i 
                LEFT JOIN categories c ON i.category_id = c.id 
                WHERE i.id = :id AND i.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau revenu
     */
    public function createIncome($data, $userId)
    {
        $incomeData = [
            'description' => htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8'),
            'amount' => floatval($data['amount']),
            'income_date' => $data['date'],
            'category_id' => !empty($data['category_id']) ? intval($data['category_id']) : null,
            'user_id' => $userId
        ];

        // Validation
        if ($incomeData['amount'] <= 0) {
            return false;
        }

        if (!$this->validateDate($incomeData['income_date'])) {
            return false;
        }

        return $this->create($incomeData);
    }

    /**
     * Met à jour un revenu
     */
    public function updateIncome($id, $data, $userId)
    {
        // Vérifier que le revenu appartient à l'utilisateur
        $income = $this->getByIdAndUser($id, $userId);
        if (!$income) {
            return false;
        }

        $updateData = [
            'description' => htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8'),
            'amount' => floatval($data['amount']),
            'income_date' => $data['date'],
            'category_id' => !empty($data['category_id']) ? intval($data['category_id']) : null
        ];

        // Validation
        if ($updateData['amount'] <= 0) {
            return false;
        }

        if (!$this->validateDate($updateData['income_date'])) {
            return false;
        }

        return $this->update($id, $updateData);
    }

    /**
     * Supprime un revenu avec vérification utilisateur
     */
    public function deleteIncome($id, $userId)
    {
        // Vérifier que le revenu appartient à l'utilisateur
        $income = $this->getByIdAndUser($id, $userId);
        if (!$income) {
            return false;
        }

        return $this->delete($id);
    }

    /**
     * Calcule le total des revenus
     */
    public function getTotalByUser($userId, $month = null)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(income_date, '%Y-%m') = :month";
            $params[':month'] = $month;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les revenus par catégorie
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
                    DATE_FORMAT(income_date, '%Y-%m') as month,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE user_id = :user_id 
                AND YEAR(income_date) = :year
                GROUP BY month
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':year' => $year]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}