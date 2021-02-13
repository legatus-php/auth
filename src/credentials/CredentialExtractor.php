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
 * A CredentialExtractor extracts a Credentials instance from an PSR-7
 * ServerRequestInterface.
 */
interface CredentialExtractor
{
    /**
     * Returns the credentials extracted from the request.
     *
     * If no credentials are found a MissingCredentials error MUST be thrown.
     *
     * @param Request $request
     *
     * @return Credentials
     *
     * @throws MissingCredentials
     */
    public function extractCredentialsFrom(Request $request): Credentials;
}
