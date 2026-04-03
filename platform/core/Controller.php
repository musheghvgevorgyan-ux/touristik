<?php

namespace Core;

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected Session $session;

    public function __construct()
    {
        $this->request  = App::get('request');
        $this->response = App::get('response');
        $this->session  = App::get('session');
    }

    /**
     * Render a view with layout
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        // Extract data variables for the view
        extract($data);

        // Capture view content
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Render within layout
        if ($layout) {
            $layoutPath = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }

            // Make common data available to layout
            $flash = $this->session->getAllFlash();
            $user = $this->currentUser();
            $csrfToken = $this->session->csrfToken();

            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): void
    {
        $this->response->json($data, $status);
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        $this->response->redirect($url);
    }

    /**
     * Redirect back with flash data
     */
    protected function back(array $flash = []): void
    {
        foreach ($flash as $key => $value) {
            $this->session->flash($key, $value);
        }
        $this->response->back();
    }

    /**
     * Get the currently logged-in user
     */
    protected function currentUser(): ?array
    {
        if (!$this->session->isLoggedIn()) {
            return null;
        }

        static $user = null;
        if ($user === null) {
            $userId = $this->session->userId();
            $user = \App\Models\User::find($userId);
        }
        return $user;
    }

    /**
     * Validate POST data — returns errors array (empty = valid)
     */
    protected function validate(array $rules): array
    {
        $validator = new \App\Helpers\Validator($this->request->allPost(), $rules);
        return $validator->errors();
    }
}
