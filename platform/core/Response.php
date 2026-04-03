<?php

namespace Core;

class Response
{
    public function html(string $content, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
    }

    public function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    public function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    public function notFound(string $message = 'Page not found'): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        // Will be replaced with proper view in Phase 2
        echo $message;
    }

    public function error(string $message = 'Internal server error', int $status = 500): void
    {
        http_response_code($status);
        if ($this->isApiRequest()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => $message]);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo $message;
        }
    }

    private function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return str_starts_with(parse_url($uri, PHP_URL_PATH) ?? '', '/api/');
    }
}
