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
 * Class BearerTokenCredentialExtractor.
 */
final class BearerTokenCredentialExtractor implements CredentialExtractor
{
    /**
     * @param Request $request
     *
     * @return Credentials
     *
     * @throws MissingCredentials
     */
    public function extractCredentialsFrom(Request $request): Credentials
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader === '') {
            throw new MissingCredentials('Authorization header is not present');
        }
        if (!str_starts_with('Bearer', $authHeader)) {
            throw new MissingCredentials('Authorization header does not have the Bearer prefix');
        }
        $token = trim(str_replace('Bearer', '', $authHeader));

        return new BearerToken($token);
    }
}
