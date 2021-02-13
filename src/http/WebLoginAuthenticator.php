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

use Psr\Http\Message\ServerRequestInterface as Request;
use UAParser\Exception\FileNotFoundException;

/**
 * The WebLoginAuthenticator is an opinionated authenticator that implements a very
 * traditional web authentication flow.
 *
 * The passed IdentityProvider should be a provider that finds your users/accounts/identities
 * in a persistent storage. This will only be called when LoginForm credentials
 * are received.
 *
 * The SessionAuthenticator and RememberTokenAuthenticator are there so you don't
 * have to worry about implementing all those features yourself.
 */
final class WebLoginAuthenticator implements IdentityProvider, CredentialExtractor
{
    private IdentityProvider $provider;
    private LoginCredentialExtractor $loginCredentials;
    private SessionAuthenticator $sessionAuthenticator;
    private RememberTokenAuthenticator $rememberTokenAuthenticator;

    /**
     * WebLoginAuthenticator constructor.
     *
     * @param IdentityProvider           $provider
     * @param SessionAuthenticator       $sessionAuthenticator
     * @param RememberTokenAuthenticator $rememberTokenAuthenticator
     */
    public function __construct(
        IdentityProvider $provider,
        LoginCredentialExtractor $loginCredentials,
        SessionAuthenticator $sessionAuthenticator,
        RememberTokenAuthenticator $rememberTokenAuthenticator
    ) {
        $this->provider = $provider;
        $this->loginCredentials = $loginCredentials;
        $this->sessionAuthenticator = $sessionAuthenticator;
        $this->rememberTokenAuthenticator = $rememberTokenAuthenticator;
    }

    /**
     * @param Request $request
     *
     * @return Credentials
     *
     * @throws MissingCredentials|FileNotFoundException
     */
    public function extractCredentialsFrom(Request $request): Credentials
    {
        // First we check for login credentials:
        try {
            return $this->loginCredentials->extractCredentialsFrom($request);
        } catch (MissingCredentials $e) {
        }
        // Then we try the session
        try {
            return $this->sessionAuthenticator->extractCredentialsFrom($request);
        } catch (MissingCredentials $e) {
        }
        // Lastly we try the remember me
        try {
            return $this->rememberTokenAuthenticator->extractCredentialsFrom($request);
        } catch (MissingCredentials $e) {
        }
        throw new MissingCredentials('Could not find any web credentials');
    }

    /**
     * @param Credentials $credentials
     *
     * @return Identity
     *
     * @throws InvalidCredentials
     */
    public function findIdentityFor(Credentials $credentials): Identity
    {
        $identity = null;
        try {
            $identity = $this->provider->findIdentityFor($credentials);
        } catch (InvalidCredentials $e) {
        }
        try {
            $identity = $this->sessionAuthenticator->findIdentityFor($credentials);
        } catch (InvalidCredentials $e) {
        }
        try {
            $identity = $this->rememberTokenAuthenticator->findIdentityFor($credentials);
        } catch (InvalidCredentials $e) {
        }

        if (!$identity instanceof Identity) {
            throw new InvalidCredentials('Unsupported credentials given');
        }

        return new WebLoginIdentity(
            $identity,
            $credentials,
            $this->sessionAuthenticator,
            $this->rememberTokenAuthenticator
        );
    }
}
