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

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * The RememberTokenAuthenticator implements methods to aid with remember me
 * functionality for authentication.
 *
 * It implements credential extractor so it can be used as one.
 *
 * It also can be used as an identity provider.
 *
 * It also provides methods to inject and remove a remember me token to and
 * from a Response.
 */
final class RememberTokenAuthenticator implements CredentialExtractor, IdentityProvider
{
    private RememberTokenStore $tokenStore;
    private string $cookieName;
    private $maxAge;
    private bool $secure;
    private bool $httpOnly;
    private string $sameSite;
    private string $path;

    /**
     * RememberTokenCredentialsExtractor constructor.
     */
    public function __construct(
        RememberTokenStore $tokenStore,
        string $cookieName = 'lgrem',
        int $maxAge = 3600 * 24 * 7,
        bool $secure = false,
        bool $httpOnly = true,
        string $sameSite = 'strict',
        string $path = '/'
    ) {
        $this->tokenStore = $tokenStore;
        $this->cookieName = $cookieName;
        $this->maxAge = $maxAge;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->sameSite = $sameSite;
        $this->path = $path;
    }

    /**
     * @param Request $request
     *
     * @return ClientRememberToken
     *
     * @throws MissingCredentials
     */
    public function extractCredentialsFrom(Request $request): ClientRememberToken
    {
        $tokenString = FigRequestCookies::get($request, $this->cookieName)->getValue();
        if ($tokenString === null) {
            throw new MissingCredentials(sprintf('RememberTokenLookup not found: cookie name "%s" is not present', $this->cookieName));
        }

        return ClientRememberToken::parse($tokenString);
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
        if (!$credentials instanceof ClientRememberToken) {
            throw new InvalidCredentials('Unsupported credentials object given');
        }

        try {
            $rememberToken = $this->tokenStore->retrieve($credentials->getTokenId());
        } catch (TokenNotFound $e) {
            throw new InvalidCredentials(sprintf('Remember token of id %s could not be found', $credentials->getTokenId()), 0, $e);
        }

        if (!$rememberToken->isValid($credentials->getValidator())) {
            throw new InvalidCredentials('Token has been found but is not valid');
        }

        if ($rememberToken->isExpired()) {
            $this->tokenStore->remove($rememberToken);
            throw new InvalidCredentials('Token has been found but is expired');
        }

        return $rememberToken;
    }

    /**
     * Stores a remember me cookie into the response.
     *
     * This method is meant to be called after you have registered the login
     * event in your system and you are providing a response.
     *
     * @param Response  $response
     * @param LoginForm $credentials
     * @param Identity  $identity
     *
     * @return Response
     */
    public function injectRememberCookie(Response $response, LoginForm $credentials, Identity $identity): Response
    {
        if ($credentials->shouldRemember()) {
            $validator = '';
            $token = RememberToken::generate($identity->toString(), $this->maxAge, $validator);
            $this->tokenStore->store($token);
            $tokenInfo = new ClientRememberToken($token->getId(), $validator);

            $setCookie = SetCookie::create($this->cookieName, $tokenInfo->toString())
                ->withPath($this->path)
                ->withMaxAge($this->maxAge)
                ->withSameSite(SameSite::fromString($this->sameSite))
                ->withHttpOnly($this->httpOnly)
                ->withSecure($this->secure);

            $response = FigResponseCookies::set($response, $setCookie);
        }

        return $response;
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    public function removeRememberCookie(Response $response, Identity $identity): Response
    {
        if ($identity instanceof RememberToken) {
            $this->tokenStore->remove($identity);
        }
        $setCookie = SetCookie::createExpired($this->cookieName);

        return FigResponseCookies::set($response, $setCookie);
    }
}
