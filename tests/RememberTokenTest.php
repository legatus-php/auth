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

use PHPUnit\Framework\TestCase;

class RememberTokenTest extends TestCase
{
    public function testItIsGenerated(): void
    {
        $validator = '';
        $token = RememberToken::generate('12345', 3600, $validator);

        self::assertSame(32, strlen($validator));
        self::assertSame(32, strlen($token->getId()));
        self::assertTrue($token->isValid($validator));
    }
}
