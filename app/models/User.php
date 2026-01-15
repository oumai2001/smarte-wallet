<?php

/**
 * Model User
 * Gère les opérations liées aux utilisateurs
 */
class User extends Model
{
    protected $table = 'users';

    private $id;
    private $fullName;
    private $email;
    private $password;
    private $isVerified;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getId() { return $this->id; }
    public function getFullName() { return $this->fullName; }
    public function getEmail() { return $this->email; }
    public function getIsVerified() { return $this->isVerified; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = password_hash($password, PASSWORD_DEFAULT); }
    public function setIsVerified($isVerified) { $this->isVerified = $isVerified; }

    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail($email)
    {
        return $this->findOne(['email' => $email]);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function register($fullName, $email, $password)
    {
        // Vérifier si l'email existe déjà
        if ($this->findByEmail($email)) {
            return false;
        }

        $data = [
            'full_name' => htmlspecialchars(trim($fullName), ENT_QUOTES, 'UTF-8'),
            'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_verified' => 0
        ];

        return $this->create($data);
    }

    /**
     * Vérifie les credentials de connexion
     */
    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Met à jour le dernier login
     */
    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }

    /**
     * Change le mot de passe
     */
    public function changePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Vérifie le compte utilisateur
     */
    public function verifyAccount($userId)
    {
        return $this->update($userId, ['is_verified' => 1]);
    }

    /**
     * Récupère les statistiques d'un utilisateur
     */
    public function getUserStats($userId)
    {
        $sql = "SELECT 
                    (SELECT COALESCE(SUM(amount), 0) FROM incomes WHERE user_id = :id1) as total_income,
                    (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE user_id = :id2) as total_expense,
                    (SELECT COUNT(*) FROM incomes WHERE user_id = :id3) as income_count,
                    (SELECT COUNT(*) FROM expenses WHERE user_id = :id4) as expense_count";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id1' => $userId,
            ':id2' => $userId,
            ':id3' => $userId,
            ':id4' => $userId
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}