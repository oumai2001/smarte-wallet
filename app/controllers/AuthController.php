<?php

/**
 * AuthController
 * Gère l'authentification des utilisateurs
 */
class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    /**
     * Page de connexion
     */
    public function login()
    {
        // Si déjà connecté, rediriger vers dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $data = [
                'title' => 'Connexion',
                'error' => $this->getFlash('error'),
                'success' => $this->getFlash('success')
            ];

            $this->view('auth/login', $data);
        }
    }

    /**
     * Traitement de la connexion
     */
    private function processLogin()
    {
        // Validation CSRF
        try {
            $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->setFlash('error', 'Token de sécurité invalide');
            $this->redirect('auth/login');
        }

        // Validation des champs
        if (!$this->validatePost(['email', 'password'])) {
            $this->setFlash('error', 'Tous les champs sont obligatoires');
            $this->redirect('auth/login');
        }

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Validation email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email invalide');
            $this->redirect('auth/login');
        }

        // Authentification
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];

            // Mettre à jour le dernier login
            $this->userModel->updateLastLogin($user['id']);

            $this->redirect('dashboard');
        } else {
            $this->setFlash('error', 'Email ou mot de passe incorrect');
            $this->redirect('auth/login');
        }
    }

    /**
     * Page d'inscription
     */
    public function register()
    {
        // Si déjà connecté, rediriger vers dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegister();
        } else {
            $data = [
                'title' => 'Inscription',
                'error' => $this->getFlash('error')
            ];

            $this->view('auth/register', $data);
        }
    }

    /**
     * Traitement de l'inscription
     */
    private function processRegister()
    {
        // Validation CSRF
        try {
            $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->setFlash('error', 'Token de sécurité invalide');
            $this->redirect('auth/register');
        }

        // Validation des champs
        if (!$this->validatePost(['full_name', 'email', 'password', 'confirm_password'])) {
            $this->setFlash('error', 'Tous les champs sont obligatoires');
            $this->redirect('auth/register');
        }

        $fullName = $this->clean($_POST['full_name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validations
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email invalide');
            $this->redirect('auth/register');
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'Le mot de passe doit contenir au moins 6 caractères');
            $this->redirect('auth/register');
        }

        if ($password !== $confirmPassword) {
            $this->setFlash('error', 'Les mots de passe ne correspondent pas');
            $this->redirect('auth/register');
        }

        // Vérifier si l'email existe déjà
        if ($this->userModel->findByEmail($email)) {
            $this->setFlash('error', 'Cet email est déjà utilisé');
            $this->redirect('auth/register');
        }

        // Créer l'utilisateur
        $userId = $this->userModel->register($fullName, $email, $password);

        if ($userId) {
            // Récupérer l'utilisateur créé
            $user = $this->userModel->findById($userId);

            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];

            $this->setFlash('success', 'Bienvenue ' . $fullName . ' !');
            $this->redirect('dashboard');
        } else {
            $this->setFlash('error', 'Erreur lors de la création du compte');
            $this->redirect('auth/register');
        }
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        // Détruire toutes les variables de session
        $_SESSION = [];

        // Détruire le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Détruire la session
        session_destroy();

        $this->redirect('auth/login');
    }
}