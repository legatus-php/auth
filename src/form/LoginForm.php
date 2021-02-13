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
 * The LoginForm represents credentials obtained through a web sign in form.
 */
class LoginForm implements Credentials
{
    private string $identifier;
    private string $password;
    private bool $remember;

    /**
     * LoginForm constructor.
     *
     * @param string $identifier
     * @param string $password
     */
    public function __construct(string $identifier, string $password, bool $remember)
    {
        $this->identifier = $identifier;
        $this->password = $password;
        $this->remember = $remember;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function shouldRemember(): bool
    {
        return $this->remember;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->identifier.':'.$this->password;
    }
}
