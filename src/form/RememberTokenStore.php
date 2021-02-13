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
 * Interface RememberTokenStore.
 */
interface RememberTokenStore
{
    /**
     * Retrieves a remember me token.
     *
     * This method SHOULD retrieve the token without performing any kind
     * of validation on the expiry date.
     *
     * Validation of the token hash MUST be performed by client code.
     *
     * @throws TokenNotFound
     */
    public function retrieve(string $id): RememberToken;

    /**
     * Removes a remember token.
     */
    public function remove(RememberToken $token): void;

    /**
     * Stores a remember token.
     */
    public function store(RememberToken $token): void;
}
