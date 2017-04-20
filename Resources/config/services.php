<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\ApiBundle\Action\ApiActionInterface;
use Ekyna\Bundle\ApiBundle\Services\Security\TokenAuthenticator;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Routing loader
        ->set('ekyna_api.routing.resource')
            ->parent('ekyna_resource.routing.resource_loader')
            ->args([
                'api_resource',
                ApiActionInterface::class,
                abstract_arg('Api routing prefix'),
                param('kernel.default_locale'),
                param('kernel.environment'),
            ])
            ->tag('routing.loader')

        // Authenticator
        ->set('ekyna_api.security.authenticator.token', TokenAuthenticator::class)
    ;
};
