<?php

require 'vendor/autoload.php';

use Antares\Router\Router;
use Antares\Router\Attributes\Get;
use Antares\Router\Attributes\Post;

class PatientController
{
    #[Get('/patients')]
    public function list(): array
    {
        return [];
    }

    #[Get('/patients/{id}')]
    public function show(int $id): array
    {
        return [];
    }

    #[Post('/patients')]
    public function create(): array
    {
        return [];
    }
}

$router = new Router();
$router->register(PatientController::class);

print_r($router->match('GET', '/patients'));

print_r($router->match('GET', '/patients/42'));

print_r($router->match('POST', '/patients'));