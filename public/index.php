<?php
// Point d'entrée de l'application
 // Toutes les requêtes passent par ce fichier

// Charger la configuration
require_once '../config/config.php';

// Charger les classes Core
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/core/Controller.php';
require_once '../app/core/App.php';

// Démarrer l'application
$app = new App();