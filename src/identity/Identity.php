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
 * Interface Identity.
 */
interface Identity
{
    /**
     * Returns the string representation of this identity.
     *
     * @return string
     */
    public function toString(): string;
}
