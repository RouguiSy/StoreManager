<?php

require_once dirname(__DIR__) . '/Controller/POSController.php';
require_once dirname(__DIR__) . '/Controller/DettesController.php';

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->routes = [
            'index' => ['controller' => 'POSController', 'method' => 'index'],
            'addToCart' => ['controller' => 'POSController', 'method' => 'addToCart'],
            'removeFromCart' => ['controller' => 'POSController', 'method' => 'removeFromCart'],
            'clearCart' => ['controller' => 'POSController', 'method' => 'clearCart'],
            'setClient' => ['controller' => 'POSController', 'method' => 'setClient'],
            'setPayment' => ['controller' => 'POSController', 'method' => 'setPayment'],
            'validateSale' => ['controller' => 'POSController', 'method' => 'validateSale'],
            'dettes' => ['controller' => 'DettesController', 'method' => 'index'],
            'repay' => ['controller' => 'DettesController', 'method' => 'repay'],
        ];
    }

    public function dispatch(): void
    {
        $action = $_GET['action'] ?? 'index';

        if (!isset($this->routes[$action])) {
            $this->notFound();
            return;
        }

        $route = $this->routes[$action];
        $controllerName = $route['controller'];
        $methodName = $route['method'];

        if (!class_exists($controllerName)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            $this->notFound();
            return;
        }

        $controller->$methodName();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "<h1>Page non trouvée</h1>";
        echo "<p>L'action demandée n'existe pas.</p>";
        echo "<a href='?action=index'>Retour à l'accueil</a>";
        exit;
    }
}
