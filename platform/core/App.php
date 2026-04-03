<?php

namespace Core;

class App
{
    private static array $container = [];
    private Router $router;

    public function __construct()
    {
        // Define base path
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }

        // Register autoloader
        spl_autoload_register([$this, 'autoload']);

        // Set error handling
        $this->setupErrorHandling();

        // Boot core services
        $this->boot();

        // Load routes
        $this->router = new Router();
        $app = $this; // Make $app available in routes.php
        require BASE_PATH . '/config/routes.php';
        self::set('router', $this->router);
    }

    private function boot(): void
    {
        // Security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Core services
        self::set('session', new Session());
        self::set('request', new Request());
        self::set('response', new Response());
    }

    public function run(): void
    {
        $request = self::get('request');
        $response = self::get('response');

        // Resolve route
        $route = $this->router->resolve($request);

        if ($route === null) {
            $response->notFound();
            return;
        }

        // Run middleware chain
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            $result = $middleware->handle($request);

            if ($result !== true) {
                // Middleware returned a response (redirect, error, etc.)
                return;
            }
        }

        // Instantiate controller and call action
        $controllerClass = $route['controller'];
        $action = $route['action'];
        $params = $route['params'];

        if (!class_exists($controllerClass)) {
            $response->error("Controller not found: {$controllerClass}", 500);
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $response->error("Action not found: {$controllerClass}::{$action}", 500);
            return;
        }

        // Call the controller action with route parameters
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * PSR-4 style autoloader
     */
    private function autoload(string $class): void
    {
        // Namespace → directory mapping
        $map = [
            'Core\\'              => BASE_PATH . '/core/',
            'App\\Controllers\\'  => BASE_PATH . '/app/Controllers/',
            'App\\Models\\'       => BASE_PATH . '/app/Models/',
            'App\\Services\\'     => BASE_PATH . '/app/Services/',
            'App\\Suppliers\\'    => BASE_PATH . '/app/Suppliers/',
            'App\\Middleware\\'   => BASE_PATH . '/app/Middleware/',
            'App\\Helpers\\'      => BASE_PATH . '/app/Helpers/',
        ];

        foreach ($map as $prefix => $dir) {
            if (str_starts_with($class, $prefix)) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }

    private function setupErrorHandling(): void
    {
        $config = require BASE_PATH . '/config/app.php';

        if ($config['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        set_exception_handler(function (\Throwable $e) {
            $config = require BASE_PATH . '/config/app.php';

            // Log the error
            $logFile = BASE_PATH . '/logs/error.log';
            $logEntry = sprintf(
                "[%s] %s in %s:%d\n%s\n\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            file_put_contents($logFile, $logEntry, FILE_APPEND);

            if ($config['debug']) {
                echo '<h1>Error</h1>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            } else {
                http_response_code(500);
                echo 'An unexpected error occurred.';
            }
        });
    }

    // --- Service Container ---

    public static function set(string $key, mixed $value): void
    {
        self::$container[$key] = $value;
    }

    public static function get(string $key): mixed
    {
        return self::$container[$key] ?? throw new \RuntimeException("Service not found: {$key}");
    }

    public static function has(string $key): bool
    {
        return isset(self::$container[$key]);
    }

    /**
     * Get the Router instance (used by routes.php)
     */
    public function router(): Router
    {
        return $this->router;
    }
}
