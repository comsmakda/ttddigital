<?php

namespace App\Core;

class View
{
    /**
     * Render sebuah view di dalam layout utama.
     */
    public static function render(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View tidak ditemukan: {$view}";
            return;
        }

        // Render konten view ke buffer, lalu suntikkan ke $content di layout
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . '/Views/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            echo $content;
            return;
        }

        require $layoutPath;
    }

    /**
     * Render tanpa layout (mis. untuk fragmen/partial khusus).
     */
    public static function renderRaw(string $view, array $data = []): void
    {
        self::render($view, $data, null);
    }
}
