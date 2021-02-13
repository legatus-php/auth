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
 * A Principal represents the authentication context in which a particular
 * Identity is operating. It also contains the credentials used to find the
 * identity.
 *
 * Different principal implementations can be used to pass information about
 * the context in which the authentication process took place.
 */
interface Principal
{
    /**
     * The identity in use for the current principal.
     *
     * @return Identity
     */
    public function getIdentity(): Identity;

    /**
     * The credentials in use for the current principal.
     *
     * @return Credentials
     */
    public function getCredentials(): Credentials;
}
