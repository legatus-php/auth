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

/**
 * Class WebLoginIdentity.
 */
final class WebLoginIdentity implements Identity
{
    private Identity $identity;
    private Credentials $credentials;
    private SessionAuthenticator $sessionAuthenticator;
    private RememberTokenAuthenticator $rememberTokenAuthenticator;

    /**
     * WebLoginIdentity constructor.
     *
     * @param Identity                   $identity
     * @param SessionAuthenticator       $sessionAuthenticator
     * @param RememberTokenAuthenticator $rememberTokenAuthenticator
     */
    public function __construct(
        Identity $identity,
        Credentials $credentials,
        SessionAuthenticator $sessionAuthenticator,
        RememberTokenAuthenticator $rememberTokenAuthenticator
    ) {
        $this->identity = $identity;
        $this->credentials = $credentials;
        $this->sessionAuthenticator = $sessionAuthenticator;
        $this->rememberTokenAuthenticator = $rememberTokenAuthenticator;
    }

    /**
     * @return Identity
     */
    public function getInnerIdentity(): Identity
    {
        return $this->identity;
    }

    /**
     * Process the web authentication.
     *
     * This method only works when the stored credentials are LoginForm.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        if ($this->credentials instanceof LoginForm) {
            $this->sessionAuthenticator->storeIdentityInSession($request, $this->identity);

            return $this->rememberTokenAuthenticator->injectRememberCookie($response, $this->credentials, $this->identity);
        }

        return $response;
    }

    /**
     * When credentials are Session ones, removes the user id from the session.
     *
     * It also removes the remember token if any.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        $this->sessionAuthenticator->removeSessionAttribute($request);

        $identity = $this->identity;
        if (!$identity instanceof RememberToken) {
            // Try to get a remember token from the request
            try {
                $credentials = $this->rememberTokenAuthenticator->extractCredentialsFrom($request);
                $identity = $this->rememberTokenAuthenticator->findIdentityFor($credentials);
            } catch (AuthenticationError $e) {
            }
        }

        return $this->rememberTokenAuthenticator->removeRememberCookie($response, $identity);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->identity->toString();
    }
}
