<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\AbstractAction;
use Ekyna\Bundle\ResourceBundle\Action\SerializerTrait;
use Ekyna\Bundle\ResourceBundle\Action\TranslatorTrait;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Model\ResourceInterface;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Class AbstractApiAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractApiAction extends AbstractAction implements ApiActionInterface
{
    use SerializerTrait;
    use TranslatorTrait;


    /**
     * Returns the serialization context.
     *
     * @param array $context
     *
     * @return array
     */
    protected function getSerializationContext(array $context = []): array
    {
        if (!isset($this->options['serialization_groups']) || empty($groups = $this->options['serialization_groups'])) {
            return $context;
        }

        $context['groups'] = $groups;

        return $context;
    }

    /**
     * Builds the JSON response.
     *
     * @param ResourceInterface|array|string $content
     * @param int                            $code
     * @param array                          $headers
     *
     * @return JsonResponse
     */
    protected function respond($content, int $code = JsonResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        if (is_null($content)) {
            return new JsonResponse($content, $code, $headers);
        }

        if ($content instanceof ResourceInterface) {
            $content = [
                $this->context->getConfig()->getName() => $content,
            ];
        }

        if (is_array($content)) {
            $content = $this
                ->getSerializer()
                ->serialize($content, 'json', $this->getSerializationContext());
        } elseif (!is_string($content)) {
            throw new UnexpectedTypeException($content, [ResourceInterface::class, 'array', 'string']);
        }

        return new JsonResponse($content, $code, $headers, is_string($content));
    }

    /**
     * Deserializes the resource.
     *
     * @param ResourceInterface $resource
     * @param string            $data
     */
    protected function deserializeResource(ResourceInterface $resource, string $data): void
    {
        $this
            ->serializer
            ->deserialize(
                $data,
                $this->context->getConfig()->getEntityClass(),
                'json',
                $this->getSerializationContext([
                    AbstractNormalizer::OBJECT_TO_POPULATE => $resource,
                ]),
            );
    }

    /**
     * Responds with validation errors.
     *
     * @param array $violations
     *
     * @return JsonResponse
     */
    protected function respondValidationErrors(array $violations): JsonResponse
    {
        if (empty($violations)) {
            throw new LogicException('Expected at least one valiation violation.');
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] =
                $this->trans($violation->getMessage(), $violation->getParameters());
        }

        return $this->respond(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Responds with resource event errors.
     *
     * @param ResourceEventInterface $event
     *
     * @return JsonResponse
     */
    protected function respondEventErrors(ResourceEventInterface $event): JsonResponse
    {
        if (!$event->hasErrors()) {
            throw new LogicException('Expected event with at least one error.');
        }

        $errors = [];
        foreach ($event->getMessages(ResourceMessage::TYPE_ERROR) as $message) {
            $errors[] = $message->trans($this->translator);
        }

        return $this->respond(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}
