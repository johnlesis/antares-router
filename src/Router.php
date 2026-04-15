<?php

declare(strict_types=1);

namespace Antares\Router;

use Antares\Router\Attributes\Delete;
use Antares\Router\Attributes\Get;
use Antares\Router\Attributes\Patch;
use Antares\Router\Attributes\Post;
use Antares\Router\Attributes\Put;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use ReflectionClass;
use RuntimeException;

final class Router
{   
    private array $routes = [];

    public function register(string $className): void
    {
        $reflectionClass = new ReflectionClass($className);
        $methods = $reflectionClass->getMethods();

        $routeAttributes = [
            Get::class    => 'GET',
            Post::class   => 'POST',
            Put::class    => 'PUT',
            Patch::class  => 'PATCH',
            Delete::class => 'DELETE',
        ];

        foreach ($methods as $method) {
            foreach ($routeAttributes as $attributeClass => $httpMethod) {
                $attributes = $method->getAttributes($attributeClass);
                if (!empty($attributes)) {
                        $routeAttribute = $attributes[0]->newInstance();
                    $path = $routeAttribute->path;
                    $name = $method->getName();
                    
                    $this->routes[] = [
                        $httpMethod,
                        $path,
                        $className,
                        $name,
                        $routeAttribute->statusCode
                    ];
                }
            }
         
        }
    }

    public function match(string $httpMethod, string $uri): array
    {
        $dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route[0], $route[1], [$route[2], $route[3], $route[4]]);
            }
        });

        $result = $dispatcher->dispatch($httpMethod, $uri);

        return match($result[0]) {
            Dispatcher::FOUND => [
                $result[1][0],  
                $result[1][1],  
                $result[2],     
                $result[1][2], 
            ],
            Dispatcher::NOT_FOUND         => throw new RuntimeException('Route not found', 404),
            Dispatcher::METHOD_NOT_ALLOWED => throw new RuntimeException('Method not allowed', 405),
        };
    }
}
