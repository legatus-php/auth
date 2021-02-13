<?php

declare(strict_types=1);

/**
 * @project Legatus Auth
 * @link https://github.com/legatus-php/auth
 * @package legatus/auth
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 Matias Navarro-Carter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Legatus\Http;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * The AuthenticationMiddleware in in charge of creating a Principal, which
 * represents the authentication context of a particular request.
 *
 * This middleware simply creates that context and is not concerned with
 * determining when authentication fails. It is the responsibility of subsequent
 * middleware to analyze the principal and determine if authentication has failed.
 *
 * For example, if a Principal contains a LoginForm credentials object but an
 * Anonymous identity, that means that the identity could not be found from
 * those credentials, which should return an error to the login form.
 */
final class AuthenticationMiddleware implements MiddlewareInterface, CredentialExtractor, IdentityProvider
{
    private CredentialExtractor $credentials;
    private IdentityProvider $provider;

    /**
     * AuthenticationMiddleware constructor.
     *
     * @param CredentialExtractor $credentials
     * @param IdentityProvider    $provider
     */
    public function __construct(CredentialExtractor $credentials, IdentityProvider $provider)
    {
        $this->credentials = $credentials;
        $this->provider = $provider;
    }

    /**
     * @param Request $request
     * @param Handler $handler
     *
     * @return Response
     */
    public function process(Request $request, Handler $handler): Response
    {
        $credentials = $this->extractCredentialsFrom($request);
        $identity = $this->findIdentityFor($credentials);
        $principal = new StdPrincipal($identity, $credentials);

        return $handler->handle($request->withAttribute('principal', $principal));
    }

    /**
     * @param Request $request
     *
     * @return Credentials
     */
    public function extractCredentialsFrom(Request $request): Credentials
    {
        try {
            return $this->credentials->extractCredentialsFrom($request);
        } catch (MissingCredentials $e) {
            return new VoidCredentials($e);
        }
    }

    /**
     * @param Credentials $credentials
     *
     * @return Identity
     */
    public function findIdentityFor(Credentials $credentials): Identity
    {
        try {
            return $this->provider->findIdentityFor($credentials);
        } catch (InvalidCredentials $e) {
            return new Anonymous($e);
        }
    }
}
