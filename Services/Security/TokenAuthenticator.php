<?php

declare(strict_types=1);

namespace Ekyna\Bundle\ApiBundle\Services\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class TokenAuthenticator
 * @package Ekyna\Bundle\ApiBundle\Services\Security
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TokenAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private ApiProviderInterface $provider;

    public function setProvider(ApiProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Auth header required', 401);
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-Auth-Token');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $apiToken = $request->headers->get('X-Auth-Token');
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport($this->provider->provide($apiToken));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
