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

/**
 * An IdentityProvider abstracts away finding an identity somewhere.
 */
interface IdentityProvider
{
    /**
     * Returns an identity that is valid for the passed credentials.
     *
     * If no identity can be found from the passed credentials, then an
     * InvalidCredentials error MUST be thrown.
     *
     * @param Credentials $credentials
     *
     * @return Identity
     *
     * @throws InvalidCredentials
     */
    public function findIdentityFor(Credentials $credentials): Identity;
}
