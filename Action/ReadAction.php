<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Component\Resource\Action\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReadAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ReadAction extends AbstractApiAction
{
    public const NAME = 'api_read';

    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $resource = $this->context->getResource();

        $context = $this->getSerializationContext();

        $content = $this->getSerializer()->serialize($resource, 'json', $context);

        return $this->respond($content);
    }

    /**
     * @inheritDoc
     */
    public static function configureAction(): array
    {
        return [
            'name'       => static::NAME,
            'permission' => Permission::READ,
            'route'      => [
                'name'     => 'api_%s_read',
                'resource' => true,
                'methods'  => 'GET',
            ],
            'options'    => [
                'serialization_groups' => ['Default'],
            ],
        ];
    }
}
