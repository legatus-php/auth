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
 * Class StdPrincipal.
 */
final class StdPrincipal implements Principal
{
    private Identity $identity;
    private Credentials $credentials;

    /**
     * StdPrincipal constructor.
     *
     * @param Identity    $identity
     * @param Credentials $credentials
     */
    public function __construct(Identity $identity, Credentials $credentials)
    {
        $this->identity = $identity;
        $this->credentials = $credentials;
    }

    /**
     * @return Identity
     */
    public function getIdentity(): Identity
    {
        return $this->identity;
    }

    /**
     * @return Credentials
     */
    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }
}
