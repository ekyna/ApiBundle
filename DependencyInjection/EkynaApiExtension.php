<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\DependencyInjection;

use Ekyna\Component\User\Service\SecurityConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

use function trim;

/**
 * Class EkynaApiExtension
 * @package Ekyna\Bundle\ApiBundle\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaApiExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->prependSecurity($config, $container);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        // replace routing prefix argument
        $container
            ->getDefinition('ekyna_api.routing.resource')
            ->replaceArgument(2, $config['routing_prefix']);
    }

    private function prependSecurity(array $config, ContainerBuilder $container): void
    {
        $routingPrefix = '/' . trim($config['routing_prefix'], '/');

        $configurator = new SecurityConfigurator();
        $configurator->configure($container, [
            'firewalls' => [
                'api' => [
                    '_priority'             => 1024,
                    'pattern'               => "^$routingPrefix",
                    'stateless'             => true,
                    'custom_authenticators' => [
                        'ekyna_api.security.authenticator.token',
                    ],
                    /*'oauth'       => [
                        'resource_owners'     => [
                            'google' => $routingPrefix.'/login/check-google',
                        ],
                        'oauth_user_provider' => [
                            'service' => 'ekyna_admin.security.oauth_user_provider',
                        ],
                        'login_path'          => 'admin_security_login',
                        'check_path'          => 'admin_security_check',
                        'failure_path'        => 'admin_security_login',
                        'default_target_path' => 'admin_dashboard',
                        'use_forward'         => false,
                        //'success_handler' => 'ekyna_admin.security.authentication_success_handler',
                        //'failure_handler' => 'ekyna_admin.security.authentication_failure_handler',
                    ],*/
                ],
            ],
        ]);
    }
}
