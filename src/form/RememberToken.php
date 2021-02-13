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
 * A RememberToken represents a secure token associated to an identity.
 */
class RememberToken implements Identity
{
    private string $id;
    private string $identity;
    private string $hashedValidator;
    private int $expires;

    /**
     * @param string $identity
     * @param int    $ttl
     * @param string $validator
     *
     * @return RememberToken
     */
    public static function generate(string $identity, int $ttl, string &$validator): RememberToken
    {
        try {
            $validator = bin2hex(random_bytes(16));
            $id = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            throw new \RuntimeException('Not enough entropy');
        }

        return new self(
            $id,
            $identity,
            hash('sha256', $validator),
            time() + $ttl,
        );
    }

    /**
     * RememberMeToken constructor.
     */
    public function __construct(string $id, string $userId, string $hashedValidator, int $expires)
    {
        $this->id = $id;
        $this->identity = $userId;
        $this->hashedValidator = $hashedValidator;
        $this->expires = $expires;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function isExpired(): bool
    {
        return $this->expires < time();
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->expires;
    }

    public function isValid(string $validator): bool
    {
        return hash_equals(hash('sha256', $validator), $this->hashedValidator);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->identity;
    }
}
