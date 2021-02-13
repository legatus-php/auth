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
use UAParser\Parser;

/**
 * Class SessionAuthenticator uses the Session to extract credentials and provide
 * an Identity.
 *
 * Session MUST have been initialized before the authentication middleware.
 *
 * This authenticator DOES NOT validate that the Identity still exists and is
 * valid.
 */
final class SessionAuthenticator implements CredentialExtractor, IdentityProvider
{
    private string $attributeName;

    /**
     * SessionAuthenticator constructor.
     *
     * @param string $attributeName
     */
    public function __construct(string $attributeName = 'auth_identity')
    {
        $this->attributeName = $attributeName;
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
        try {
            $session = SessionContext::from($request);
        } catch (\RuntimeException $e) {
            throw new MissingCredentials('No session found in the request');
        }

        $authId = $session->get($this->attributeName);
        if ($authId === null) {
            throw new MissingCredentials(sprintf('Session does not contain attribute "%s"', $this->attributeName));
        }

        $userAgent = $request->getHeaderLine('User-Agent');

        /** @var Parser $parser */
        $parser = Parser::create();
        $result = $parser->parse($userAgent);

        return new SessionInfo(
            $authId,
            $this->extractClientIp($request),
            $result->device->family,
            $result->ua->family,
            $result->os->family
        );
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function extractClientIp(Request $request): string
    {
        $ipAddress = '';
        if ($request->hasHeader('X-Forwarded-For')) {
            $ips = array_map('trim', explode(',', $request->getHeaderLine('X-Forwarded-For')));
            // Return the leftmost ip address
            $ipAddress = $ips[0] ?? '';
        }
        // TODO: Add the Forwarded header
        // Google
        if ($ipAddress === '' && $request->hasHeader('X-ProxyUser-Ip')) {
            $ipAddress = $request->getHeaderLine('X-ProxyUser-Ip');
        }
        if ($ipAddress === '' && $request->hasHeader('X-Real-Ip')) {
            $ipAddress = $request->getHeaderLine('X-Real-Ip');
        }
        if ($ipAddress === '') {
            $ipAddress = $request->getServerParams()['REMOTE_ADDR'] ?? '';
        }

        return $ipAddress;
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
        if (!$credentials instanceof SessionInfo) {
            throw new InvalidCredentials('Unsupported credentials provided');
        }

        return $credentials;
    }

    /**
     * @param Request  $request
     * @param Identity $identity
     */
    public function storeIdentityInSession(Request $request, Identity $identity): void
    {
        $session = SessionContext::from($request);
        $session->put($this->attributeName, $identity->toString());
    }
}
