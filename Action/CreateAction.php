<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\FactoryTrait;
use Ekyna\Bundle\ResourceBundle\Action\ManagerTrait;
use Ekyna\Bundle\ResourceBundle\Action\ValidatorTrait;
use Ekyna\Component\Resource\Action\Permission;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreateAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CreateAction extends AbstractApiAction
{
    use FactoryTrait;
    use ManagerTrait;
    use ValidatorTrait;

    public const NAME = 'api_create';

    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $resource = $this->createResource();

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

        return $this->respond($resource, Response::HTTP_CREATED);
    }

    /**
     * @inheritDoc
     */
    public static function configureAction(): array
    {
        return [
            'name'       => static::NAME,
            'permission' => Permission::CREATE,
            'route'      => [
                'name'    => 'api_%s_create',
                'methods' => 'POST',
            ],
            'options'    => [
                'serialization_groups' => ['Default'],
                'validation_groups'    => [],
            ],
        ];
    }
}
