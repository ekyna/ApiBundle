<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\ManagerTrait;
use Ekyna\Component\Resource\Action\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DeleteAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class DeleteAction extends AbstractApiAction
{
    use ManagerTrait;

    public const NAME = 'api_delete';


    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $resource = $this->context->getResource();

        $event = $this->remove($resource);
        if ($event->hasErrors()) {
            return $this->respondEventErrors($event);
        }

        return $this->respond(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public static function configureAction(): array
    {
        return [
            'name'       => static::NAME,
            'permission' => Permission::DELETE,
            'route'      => [
                'name'     => 'api_%s_delete',
                'methods'  => 'DELETE',
                'resource' => true,
            ],
        ];
    }
}
