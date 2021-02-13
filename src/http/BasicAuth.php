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
 * Class BasicAuth.
 */
class BasicAuth implements Credentials
{
    private string $username;
    private string $password;

    /**
     * @param string $base64
     *
     * @return BasicAuth
     *
     * @throws MissingCredentials
     */
    public static function parse(string $base64): BasicAuth
    {
        $decoded = base64_decode($base64);
        if (!is_string($decoded)) {
            throw new MissingCredentials('Invalid base 64 provided');
        }
        [$username, $password] = explode(':', $decoded, 2);

        return new self($username, $password);
    }

    /**
     * BasicAuth constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return base64_encode($this->username.':'.$this->password);
    }
}
