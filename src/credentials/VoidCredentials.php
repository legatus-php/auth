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
 * VoidCredentials is used to represent none credentials.
 */
final class VoidCredentials implements Credentials
{
    public const NAME = 'void';
    private MissingCredentials $error;

    /**
     * VoidCredentials constructor.
     *
     * @param MissingCredentials $error
     */
    public function __construct(MissingCredentials $error)
    {
        $this->error = $error;
    }

    /**
     * @return MissingCredentials
     */
    public function getError(): MissingCredentials
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
