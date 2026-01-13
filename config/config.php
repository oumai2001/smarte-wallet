<?php

/**
 * Fichier de Configuration
 */

// Configuration de la base de données
// Changez DB_DRIVER selon votre SGBD : 'mysql' ou 'pgsql'
define('DB_DRIVER', 'pgsql');  // ou 'mysql'
define('DB_HOST', 'localhost');
define('DB_PORT', 5432);  // 5432 pour PostgreSQL, 3306 pour MySQL
define('DB_NAME', 'smarte_walet');
define('DB_USER', 'postgres');  // 'postgres' pour PostgreSQL, 'root' pour MySQL
define('DB_PASS', 'oumaima fr');  // Votre mot de passe

// Configuration de l'application
define('BASE_URL', 'http://localhost/smart-wallet/public/');
define('APP_NAME', 'Smarte Wallet');
define('APP_VERSION', '2.0.0');

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 en HTTPS

// Timezone
date_default_timezone_set('Africa/Casablanca');

// Gestion des erreurs (à désactiver en production)
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}