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
 * Class SessionInfo.
 *
 * Implements both a credential and an identity, since the session is already
 * secured and validated.
 */
class SessionInfo implements Credentials, Identity
{
    private string $authId;
    private string $ip;
    private string $device;
    private string $browser;
    private string $os;

    /**
     * SessionInfo constructor.
     *
     * @param string $authId
     * @param string $ip
     * @param string $device
     * @param string $browser
     * @param string $os
     */
    public function __construct(string $authId, string $ip, string $device, string $browser, string $os)
    {
        $this->authId = $authId;
        $this->ip = $ip;
        $this->device = $device;
        $this->browser = $browser;
        $this->os = $os;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->authId;
    }
}
