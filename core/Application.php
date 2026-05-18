<?php
// container configuration
// Central application configuration and initialization

declare(strict_types=1);

namespace Core;

use App\Contracts\BloodRequestRepositoryInterface;
use App\Repositories\BloodRequestRepository;
use Core\Container\Container;
use Core\Http\Request;
use Core\Http\Router;

class Application
{
    public function __construct(
        private readonly Container $container,
        private readonly Router $router,
    ) {
    }

    public static function configure(): self
    {
        $container = new Container();

        $container->singleton(
            BloodRequestRepositoryInterface::class,
            static fn (): BloodRequestRepositoryInterface => new BloodRequestRepository()
        );

        return new self($container, new Router($container));
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(Request $request): void
    {
        $this->router->dispatch($request);
    }
}
