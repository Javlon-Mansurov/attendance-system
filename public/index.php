<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = BASE_PATH . '/src/' . $rel . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

use App\Controllers\ReportController;
use App\Controllers\WorkLogController;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;

$configFile = BASE_PATH . '/config/config.php';
if (!is_file($configFile)) {
    $configFile = BASE_PATH . '/config/config.example.php';
}
$config = require $configFile;

ini_set('display_errors', $config['debug'] ? '1' : '0');
error_reporting(E_ALL);

Database::configure($config['db']);

set_exception_handler(function (Throwable $e) use ($config): void {
    error_log((string) $e);
    http_response_code(500);
    echo (new View(BASE_PATH . '/views'))->render('errors/error', [
        'title'   => 'Хатои дохилии сервер',
        'code'    => 500,
        'message' => $config['debug'] ? $e->getMessage() : 'Баъдтар кӯшиш кунед ё ба администратор муроҷиат намоед.',
    ]);
});

$router = new Router();
$router->get('/', [WorkLogController::class, 'index']);
$router->get('/work-logs', [WorkLogController::class, 'index']);
$router->get('/work-logs/create', [WorkLogController::class, 'create']);
$router->post('/work-logs/create', [WorkLogController::class, 'store']);
$router->get('/work-logs/{id}', [WorkLogController::class, 'show']);
$router->get('/work-logs/{id}/edit', [WorkLogController::class, 'edit']);
$router->post('/work-logs/{id}/edit', [WorkLogController::class, 'update']);
$router->post('/work-logs/{id}/delete', [WorkLogController::class, 'delete']);
$router->post('/employees/{id}/delete', [WorkLogController::class, 'deleteEmployee']);
$router->get('/reports/hours', [ReportController::class, 'hours']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
