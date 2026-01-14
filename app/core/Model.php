<?php
//Classe Model de Base
 // Tous les models héritent de cette classe
class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère tous les enregistrements
     */
    public function findAll($conditions = [], $orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            foreach (array_keys($conditions) as $key) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un enregistrement par ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un enregistrement selon des conditions
     */
    public function findOne($conditions)
    {
        $where = [];
        foreach (array_keys($conditions) as $key) {
            $where[] = "$key = :$key";
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insère un nouvel enregistrement
     */
    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un enregistrement
     */
    public function update($id, $data)
    {
        $fields = [];
        foreach (array_keys($data) as $key) {
            $fields[] = "$key = :$key";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }

    /**
     * Supprime un enregistrement
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compte les enregistrements
     */
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            foreach (array_keys($conditions) as $key) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        
        return $stmt->fetchColumn();
    }

    /**
     * Exécute une requête SQL personnalisée
     */
    protected function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt;
    }
}