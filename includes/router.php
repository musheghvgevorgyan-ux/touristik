<?php

class Router {
    private $routes;
    private $defaultRoute;
    private $errorRoute;
    private $authRedirect;

    public function __construct() {
        $configFile = __DIR__ . '/../config/routes.json';
        $config = json_decode(file_get_contents($configFile), true);

        if (!$config) {
            die("Routes configuration file is missing or invalid.");
        }

        $this->routes = $config['routes'];
        $this->defaultRoute = $config['default_route'];
        $this->errorRoute = $config['error_route'];
        $this->authRedirect = $config['auth_redirect'];
    }

    public function resolve($page) {
        // Default to home if no page specified
        if (empty($page)) {
            $page = $this->defaultRoute;
        }

        // Check if route exists
        if (!isset($this->routes[$page])) {
            $page = $this->errorRoute;
        }

        $route = $this->routes[$page];

        // Check authentication
        if ($route['auth'] && !isset($_SESSION['admin'])) {
            header('Location: ' . url($this->authRedirect));
            exit;
        }

        // Check if file exists
        $filePath = __DIR__ . '/../' . $route['file'];
        if (!file_exists($filePath)) {
            $page = $this->errorRoute;
            $route = $this->routes[$page];
            $filePath = __DIR__ . '/../' . $route['file'];
        }

        return [
            'page' => $page,
            'file' => $filePath,
            'title' => $route['title'],
            'auth' => $route['auth']
        ];
    }

    public function getRoutes() {
        return $this->routes;
    }
}
