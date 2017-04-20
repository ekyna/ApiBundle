<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\AbstractAction;
use Ekyna\Bundle\ResourceBundle\Action\SearchTrait;
use Ekyna\Component\Resource\Action\Permission;
use Ekyna\Component\Resource\Search;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SearchAction extends AbstractAction implements ApiActionInterface
{
    use SearchTrait;

    public const NAME = 'api_search';

    public function __invoke(): Response
    {
        $data = [
            'results'     => [],
            'total_count' => 0,
        ];

        $searchRequest = $this->createSearchRequest($this->request);

        $repository = $this->getSearchRepository();
        if ($repository->supports($searchRequest)) {
            $data = $repository->search($searchRequest);
        }

        return new JsonResponse($data);
    }

    /**
     * Creates the search request.
     */
    protected function createSearchRequest(Request $request): Search\Request
    {
        $page  = $request->query->getInt('page', 1) - 1;
        $limit = $request->query->getInt('limit', 10);

        $searchRequest = new Search\Request($request->query->get('query'));

        foreach ($this->options['parameters'] as $parameter) {
            $searchRequest->setParameter($parameter, $request->query->get($parameter));
        }

        return $searchRequest
            ->setType(Search\Request::RAW)
            ->setPrivate(true)
            ->setLimit($limit)
            ->setOffset($page * $limit);
    }

    public static function configureAction(): array
    {
        return [
            'name'       => static::NAME,
            'permission' => Permission::SEARCH,
            'route'      => [
                'name'    => 'api_%s_search',
                'path'    => '/search',
                'methods' => 'GET',
            ],
            'options'    => [
                'serialization_group' => 'Search',
                'parameters'          => [],
            ],
        ];
    }

    public static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['serialization_group', 'parameters'])
            ->setAllowedTypes('serialization_group', 'string')
            ->setAllowedTypes('parameters', 'string[]');
    }
}
