<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Component\Resource\Action\AbstractActionBuilder;
use Ekyna\Component\Resource\Action\ActionBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CrudActions
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CrudActions extends AbstractActionBuilder implements ActionBuilderInterface
{
    protected const NAME = 'api_crud';


    public static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'list',   // api_list options
                'create', // api_create options
                'read',   // api_read options
                'update', // api_update options
                'delete', // api_delete options
                'search', // api_search options
            ])
            ->setAllowedTypes('list', ['array', 'bool', 'null'])
            ->setAllowedTypes('create', ['array', 'bool', 'null'])
            ->setAllowedTypes('read', ['array', 'bool', 'null'])
            ->setAllowedTypes('update', ['array', 'bool', 'null'])
            ->setAllowedTypes('delete', ['array', 'bool', 'null'])
            ->setAllowedTypes('search', ['array', 'bool', 'null']);
    }

    protected static function getMap(array $config): array
    {
        return [
            'list'   => ListAction::class,
            'create' => CreateAction::class,
            'read'   => ReadAction::class,
            'update' => UpdateAction::class,
            'delete' => DeleteAction::class,
            'search' => SearchAction::class,
        ];
    }
}
