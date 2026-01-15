<?php

/**
 * Model Category
 * Gère les opérations liées aux catégories
 */
class Category extends Model
{
    protected $table = 'categories';

    private $id;
    private $name;
    private $type;
    private $createdAt;

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getCreatedAt() { return $this->createdAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setType($type) { $this->type = $type; }

    /**
     * Récupère toutes les catégories par type
     */
    public function getAllByType($type = null)
    {
        if ($type && in_array($type, ['income', 'expense'])) {
            return $this->findAll(['type' => $type], 'name ASC');
        }
        
        return $this->findAll([], 'type ASC, name ASC');
    }

    /**
     * Récupère les catégories de revenus
     */
    public function getIncomeCategories()
    {
        return $this->getAllByType('income');
    }

    /**
     * Récupère les catégories de dépenses
     */
    public function getExpenseCategories()
    {
        return $this->getAllByType('expense');
    }

    /**
     * Crée une nouvelle catégorie
     */
    public function createCategory($name, $type)
    {
        // Validation
        if (!in_array($type, ['income', 'expense'])) {
            return false;
        }

        $data = [
            'name' => htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8'),
            'type' => $type
        ];

        return $this->create($data);
    }

    /**
     * Vérifie si une catégorie existe par nom et type
     */
    public function existsByNameAndType($name, $type)
    {
        $result = $this->findOne([
            'name' => $name,
            'type' => $type
        ]);
        
        return $result !== false;
    }

    /**
     * Compte les revenus/dépenses d'une catégorie
     */
    public function countUsage($categoryId, $type)
    {
        $table = $type === 'income' ? 'incomes' : 'expenses';
        
        $sql = "SELECT COUNT(*) FROM {$table} WHERE category_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $categoryId]);
        
        return $stmt->fetchColumn();
    }
}