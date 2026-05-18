<?php
// Engine.php is the view renderer. It loads view files and passes data to them.  ('/app/Views') 
declare(strict_types=1);

namespace Core\View;

use RuntimeException;

class Engine
{
    private string $viewsPath;

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? dirname(__DIR__, 2) . '/app/Views';
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data = []): void
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
