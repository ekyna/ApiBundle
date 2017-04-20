<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\ManagerTrait;
use Ekyna\Bundle\ResourceBundle\Action\ValidatorTrait;
use Ekyna\Component\Resource\Action\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UpdateAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class UpdateAction extends AbstractApiAction
{
    use ManagerTrait;
    use ValidatorTrait;

    public const NAME = 'api_update';

    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $resource = $this->context->getResource();

        $data = $this->request->getContent();

        // Deserialization
        $this->deserializeResource($resource, $data);

        // Validation
        $violations = $this->validate($resource, null, $this->options['validation_groups']);
        if ($violations->count()) {
            return $this->respondValidationErrors(iterator_to_array($violations));
        }

        // Persistence
        $event = $this->persist($resource);
        if ($event->hasErrors()) {
            return $this->respondEventErrors($event);
        }

        return $this->respond($resource, Response::HTTP_OK);
    }

    /**
     * @inheritDoc
     */
    public static function configureAction(): array
    {
        return [
            'name'       => static::NAME,
            'permission' => Permission::UPDATE,
            'route'      => [
                'name'     => 'api_%s_update',
                'methods'  => 'PUT',
                'resource' => true,
            ],
            'options'    => [
                'serialization_groups' => ['Default'],
                'validation_groups'    => [],
            ],
        ];
    }
}
