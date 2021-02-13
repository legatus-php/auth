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
 * Anonymous is an special identity that indicates that no identity could be
 * determined.
 */
final class Anonymous implements Identity
{
    public const NAME = 'anonymous';
    private InvalidCredentials $error;

    /**
     * Anonymous constructor.
     *
     * @param InvalidCredentials $error
     */
    public function __construct(InvalidCredentials $error)
    {
        $this->error = $error;
    }

    /**
     * @return InvalidCredentials
     */
    public function getError(): InvalidCredentials
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return self::NAME;
    }
}
