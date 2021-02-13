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

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * The ChainCredentialsExtractor wraps together multiple credential extractors.
 *
 * The extractors are executed in order of insertion.
 *
 * Order of insertion is important as the first credentials found are the ones
 * that will be returned.
 */
final class ChainCredentialsExtractor implements CredentialExtractor
{
    /**
     * @var CredentialExtractor[]
     */
    private array $extractors;

    /**
     * ChainCredentialsExtractor constructor.
     *
     * @param CredentialExtractor ...$extractors
     */
    public function __construct(CredentialExtractor ...$extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * @param Request $request
     *
     * @return Credentials
     *
     * @throws MissingCredentials
     */
    public function extractCredentialsFrom(Request $request): Credentials
    {
        $tried = [];
        foreach ($this->extractors as $extractor) {
            try {
                return $extractor->extractCredentialsFrom($request);
            } catch (MissingCredentials $e) {
                $tried[] = get_class($extractor);
            }
        }
        throw new MissingCredentials('Credentials not found: tried '.implode(', ', $tried));
    }
}
