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

/**
 * Class LoginCredentialExtractor.
 */
final class LoginCredentialExtractor implements CredentialExtractor
{
    private string $method;
    private string $path;
    private string $identifierField;
    private string $passwordField;
    private string $rememberMeField;

    /**
     * LoginCredentialExtractor constructor.
     *
     * @param string $method
     * @param string $path
     * @param string $identifierField
     * @param string $passwordField
     */
    public function __construct(
        string $method = 'POST',
        string $path = '/login',
        string $identifierField = 'email',
        string $passwordField = 'password',
        string $rememberMeField = 'remember'
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->identifierField = $identifierField;
        $this->passwordField = $passwordField;
        $this->rememberMeField = $rememberMeField;
    }

    /**
     * @param Request $request
     *
     * @return LoginForm
     *
     * @throws MissingCredentials
     */
    public function extractCredentialsFrom(Request $request): LoginForm
    {
        if ($this->method !== $request->getMethod() || $this->path !== $request->getUri()->getPath()) {
            throw new MissingCredentials('LoginForm not found: method and path do not match');
        }
        $body = $request->getParsedBody();
        $identifier = $body[$this->identifierField] ?? null;
        $password = $body[$this->passwordField] ?? null;
        $remember = $body[$this->rememberMeField] ?? false;
        // Fixes for remember me on checkboxes
        if ($remember === 'on' || $remember === 'true' || $remember === '1') {
            $remember = true;
        }
        if (!is_bool($remember)) {
            $remember = false;
        }
        if ($identifier === null || $password === null) {
            throw new MissingCredentials('LoginForm not found: missing identifier or password in request body');
        }

        return new LoginForm($identifier, $password, $remember);
    }
}
