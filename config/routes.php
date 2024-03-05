<?php

use App\Features\HealthCheck\HealthCheckController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
$routes
    ->add('HealthCheck', '/health-check')
    ->methods(['GET'])
    ->controller(HealthCheckController::class)

// if the action is implemented as the __invoke() method of the
// controller class, you can skip the 'method_name' part:
// ->controller(BlogController::class)
;
};
