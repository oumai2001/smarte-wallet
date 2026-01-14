<?php
// Classe Controller de Base
// Tous les contrôleurs héritent de cette classe
class Controller
{
   // Charge un model
    protected function model($model)
    {
        $modelPath = '../app/models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        }
        
        throw new Exception("Model $model not found");
    }

    // Charge une vue
    protected function view($view, $data = [])
    {
        extract($data);
        
        $viewPath = '../app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new Exception("View $view not found");
        }
    }

   // Redirige vers une URL
    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    //Vérifie si l'utilisateur est authentifié
    protected function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    //Vérifie si l'utilisateur est connecté
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Récupère l'ID de l'utilisateur connecté
    protected function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    //Récupère le nom de l'utilisateur connecté
    protected function getUserName()
    {
        return $_SESSION['user_name'] ?? 'Invité';
    }

   // Définit un message flash
    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    // Récupère et supprime un message flash
    protected function getFlash($type)
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    // Génère un token CSRF
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Vérifie le token CSRF
    protected function verifyCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }
        return true;
    }

    // Retourne une réponse JSON
     
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    
     // Nettoie les données d'entrée

    protected function clean($data)
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    //Valide les données POST
    
    protected function validatePost($required = [])
    {
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                return false;
            }
        }
        return true;
    }
}