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

use Closure;
use DirectoryIterator;
use SplFileInfo;

/**
 * The LocalFsRememberTokenStore stores remember tokens in a directory in the
 * operating system filesystem.
 *
 * This is probably a good enough method for applications that don't have a lot
 * of traffic and are usually deployed on a single server.
 *
 * For applications that are required to scale horizontally you should use
 * a RememberTokenStore implementation with efficient storage, like a Redis one.
 */
final class LocalFsRememberTokenStore implements RememberTokenStore
{
    private string $directory;

    /**
     * LocalFsRememberTokenStore constructor.
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->ensurePath();
    }

    /**
     * @throws TokenNotFound
     */
    public function retrieve(string $id): RememberToken
    {
        $filename = $this->directory.'/'.$id;
        if (!is_file($filename)) {
            throw new TokenNotFound('Token does not exist');
        }
        [$userId, $hashedValidator, $expires] = $this->readTokenFile($filename);

        return new RememberToken($id, $userId, $hashedValidator, (int) $expires);
    }

    private function readTokenFile(string $filename): array
    {
        $contents = file_get_contents($filename);

        return explode(PHP_EOL, $contents, 3);
    }

    /**
     * @param string $filename
     */
    private function deleteTokenFile(string $filename): void
    {
        unlink($filename);
    }

    /**
     * @param RememberToken $token
     */
    public function remove(RememberToken $token): void
    {
        $filename = $this->directory.'/'.$token->getId();
        $this->deleteTokenFile($filename);
    }

    /**
     * @param RememberToken $token
     */
    public function store(RememberToken $token): void
    {
        $extract = $this->buildExtractor($token);
        $contents = implode(PHP_EOL, [
            $extract('identity'),
            $extract('hashedValidator'),
            $extract('expires'),
        ]);
        $filename = $this->directory.'/'.$token->getId();
        file_put_contents($filename, $contents);
    }

    private function ensurePath(): void
    {
        if (!is_dir($this->directory) && !@mkdir($this->directory, 0755, true) && !is_dir($this->directory)) {
            throw new \RuntimeException(sprintf('Could not create directory %s', $this->directory));
        }
    }

    /**
     * @param RememberToken $token
     *
     * @return Closure
     */
    private function buildExtractor(RememberToken $token): Closure
    {
        return Closure::fromCallable(function (string $name) { return $this->{$name} ?? null; })
            ->bindTo($token, RememberToken::class);
    }

    public function cleanExpired(): void
    {
        /** @var SplFileInfo[] $directory */
        $directory = new DirectoryIterator($this->directory);
        foreach ($directory as $fileInfo) {
            if ($fileInfo->isDir()) {
                continue;
            }
            $filename = $fileInfo->getFilename();
            [$_, $_, $expires] = $this->readTokenFile($filename);
            if ($expires < time()) {
                $this->deleteTokenFile($filename);
            }
        }
    }
}
