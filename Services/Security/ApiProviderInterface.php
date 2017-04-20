<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Services\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Interface ApiProviderInterface
 * @package Ekyna\Bundle\ApiBundle\Services\Security
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface ApiProviderInterface
{
    /**
     * Creates the passport badge.
     */
    public function provide(string $token): UserBadge;
}
