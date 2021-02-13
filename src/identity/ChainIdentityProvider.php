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
 * The ChainIdentityProvider chains together multiple identity providers.
 *
 * The extractors are executed in order of insertion.
 *
 * Order of insertion is important as the first credentials found are the ones
 * that will be returned.
 */
final class ChainIdentityProvider implements IdentityProvider
{
    /**
     * @var IdentityProvider[]
     */
    private array $providers;

    /**
     * ChainIdentityProvider constructor.
     *
     * @param IdentityProvider ...$providers
     */
    public function __construct(IdentityProvider ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param IdentityProvider $provider
     */
    public function push(IdentityProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @param Credentials $credentials
     *
     * @return Identity
     *
     * @throws InvalidCredentials
     */
    public function findIdentityFor(Credentials $credentials): Identity
    {
        $tried = [];
        foreach ($this->providers as $provider) {
            try {
                return $provider->findIdentityFor($credentials);
            } catch (InvalidCredentials $e) {
                $tried[] = get_class($provider);
            }
        }
        throw new InvalidCredentials('Identity not found: tried '.implode(', ', $tried));
    }
}
