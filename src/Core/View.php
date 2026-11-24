<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function render(string $template, array $params = []): string
    {
        $file = $this->basePath . '/' . ltrim($template, '/') . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Шаблон ёфт нашуд: {$template}");
        }
        extract($params, EXTR_SKIP);
        ob_start();
        require $file;
        $content = (string) ob_get_clean();

        if (!str_starts_with($template, 'errors/')) {
            $layout = $this->basePath . '/layouts/main.php';
            if (is_file($layout)) {
                ob_start();
                require $layout;
                return (string) ob_get_clean();
            }
        }
        return $content;
    }

    public function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function url(string $path): string
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        if ($base === '.' || $base === '/') {
            $base = '';
        }
        return $base . '/' . ltrim($path, '/');
    }
}
