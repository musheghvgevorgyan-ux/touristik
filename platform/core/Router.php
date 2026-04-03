<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $controller, $method, $middleware);
    }

    public function post(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $controller, $method, $middleware);
    }

    public function group(array $attributes, callable $callback): self
    {
        $previousPrefix = $this->currentPrefix ?? '';
        $previousMiddleware = $this->currentMiddleware ?? [];

        $this->currentPrefix = $previousPrefix . ($attributes['prefix'] ?? '');
        $this->currentMiddleware = array_merge(
            $previousMiddleware,
            $attributes['middleware'] ?? []
        );

        $callback($this);

        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;

        return $this;
    }

    private string $currentPrefix = '';
    private array $currentMiddleware = [];

    private function addRoute(string $httpMethod, string $path, string $controller, string $method, array $middleware): self
    {
        $fullPath = $this->currentPrefix . $path;
        $fullMiddleware = array_merge($this->currentMiddleware, $middleware);

        // Convert route params like {id} to regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $httpMethod,
            'path'       => $fullPath,
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $method,
            'middleware'  => $fullMiddleware,
        ];

        return $this;
    }

    public function resolve(Request $request): ?array
    {
        $uri = $request->uri();
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract only named parameters
                $params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

                return [
                    'controller' => $route['controller'],
                    'action'     => $route['action'],
                    'params'     => $params,
                    'middleware'  => $route['middleware'],
                ];
            }
        }

        return null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
