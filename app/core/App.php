<?php

// Classe App - Front Controller
 // Gère le routing et le dispatch des requêtes
 
class App
{
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Vérifier si le contrôleur existe
        $controllerFile = '../app/controllers/' . ucfirst($url[0]) . 'Controller.php';
        
        if (file_exists($controllerFile)) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        // Inclure et instancier le contrôleur-
        require_once '../app/controllers/' . $this->controller . '.php';//app/controllers/
        $this->controller = new $this->controller; //cree une instence

        // Vérifier si la méthode existe
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Récupérer les paramètres
        $this->params = $url ? array_values($url) : [];

        // Appeler la méthode du contrôleur avec les paramètres
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // Parse l'URL pour extraire controller/method/params
    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);//ila kant <scripte> ktwali scripte
            $url = explode('/', $url);//
            return $url;
        }
        
        return ['dashboard'];
    }
}