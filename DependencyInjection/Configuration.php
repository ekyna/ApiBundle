<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ekyna\Bundle\ApiBundle\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('ekyna_api');

        $root = $builder->getRootNode();
        $root
            ->children()
                ->scalarNode('routing_prefix')->defaultValue('/api')->end()
            ->end()
        ;

        return $builder;
    }
}
