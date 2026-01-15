<?php

class Router {
    private $routes = [];
    private $notFoundCallback;

    /**
     * Add a GET route
     */
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Add a POST route
     */
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add any HTTP method route
     */
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    /**
     * Set 404 handler
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }

    /**
     * Run the router
     */
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_GET['url'] ?? '/';
        
        // Remove query string and trailing slashes
        $requestUri = strtok($requestUri, '?');
        $requestUri = '/' . trim($requestUri, '/');
        
        // Handle root path
        if ($requestUri === '/') {
            $requestUri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remove full match
                    return call_user_func_array($route['callback'], $matches);
                }
            }
        }

        // No route found - 404
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo "404 - Page Not Found";
        }
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex($path) {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);
        
        // Convert :param to named capture groups
        $pattern = preg_replace('/\:([a-zA-Z0-9_]+)/', '([a-zA-Z0-9_-]+)', $pattern);
        
        return '/^' . $pattern . '$/';
    }
}

