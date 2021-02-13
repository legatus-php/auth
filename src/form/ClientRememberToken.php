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
 * Class ClientRememberToken models information received from the client about a
 * remember token.
 */
final class ClientRememberToken implements Credentials
{
    private string $tokenId;
    private string $validator;

    /**
     * @param string $token
     */
    public static function parse(string $token): ClientRememberToken
    {
        [$id, $validator] = str_split($token, 32);

        return new self($id, $validator);
    }

    /**
     * RememberTokenLookup constructor.
     *
     * @param string $tokenId
     */
    public function __construct(string $tokenId, string $validator)
    {
        $this->tokenId = $tokenId;
        $this->validator = $validator;
    }

    /**
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * @return string
     */
    public function getValidator(): string
    {
        return $this->validator;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->tokenId.$this->validator;
    }
}
