<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Action;

use Ekyna\Bundle\ResourceBundle\Action\RepositoryTrait;
use Ekyna\Component\Resource\Action\Permission;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * Class ListAction
 * @package Ekyna\Bundle\ApiBundle\Action
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ListAction extends AbstractApiAction
{
    use RepositoryTrait;

    public const NAME = 'api_list';

    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $query = $this->request->query;

        $criteria = $query->get('criteria', []);
        $sorting = $query->get('sorting', []);
        $page = $query->getInt('page', 1);
        $maxPerPage = $query->getInt('max-per-page', 12);

        // TODO Parents resources criteria

        $pager = $this->getRepository()->createPager($criteria, $sorting);
        $pager
            ->setCurrentPage($page)
            ->setMaxPerPage($maxPerPage);

        try {
            $resources = (array)$pager->getCurrentPageResults();
        } catch (Throwable $previous) {
            throw new BadRequestHttpException(null, $previous);
        }

        $data = [
            'page'      => $page,
            'count'     => $pager->getNbResults(),
            'resources' => $resources,
        ];

        return $this->respond($data);
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
                'name'    => 'api_%s_list',
                'methods' => 'GET',
            ],
            'options'    => [
                'serialization_groups' => ['Default'],
            ],
        ];
    }
}
