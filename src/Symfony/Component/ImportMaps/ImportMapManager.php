<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ImportMaps;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\VarExporter\VarExporter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 */
final class ImportMapManager
{
    public const POLYFILL_URL = 'https://ga.jspm.io/npm:es-module-shims@1.7.0/dist/es-module-shims.js';

    /**
     * @see https://regex101.com/r/2cR9Rh/1
     *
     * Partially based on https://github.com/dword-design/package-name-regex
     */
    private const PACKAGE_PATTERN = '/^(?:https?:\/\/[\w\.-]+\/)?(?:(?<registry>\w+):)?(?<package>(?:@[a-z0-9-~][a-z0-9-._~]*\/)?[a-z0-9-~][a-z0-9-._~]*)(?:@(?<version>[\w\._-]+))?(?:(?<subpath>\/.*))?$/';

    private ?array $importMap = null;

    public function __construct(
        private readonly string $path,
        private readonly Provider $provider = Provider::Jspm,
        private ?HttpClientInterface $httpClient = null,
        private readonly string $api = 'https://api.jspm.io',
        private readonly Filesystem $filesystem = new Filesystem(),
    ) {
        $this->httpClient ??= HttpClient::createForBaseUri($this->api);
    }

    private function loadImportMap(): void
    {
        if (null !== $this->importMap) {
            return;
        }

        $this->importMap = $this->filesystem->exists($this->path) ? include $this->path : [];
    }

    public function getImportMap(): string
    {
        $this->loadImportMap();

        // Use JSON_UNESCAPED_SLASHES | JSON_HEX_TAG to prevent XSS
        return json_encode($this->importMap, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_HEX_TAG);
    }

    /**
     * Adds or updates packages.
     *
     * @param string[] $packages
     */
    public function require(array $packages, Env $env = Env::Production, ?Provider $provider = null): void
    {
        $this->createImportMap($env, $provider, false, $packages, []);
    }

    /**
     * Removes packages.
     *
     * @param string[] $packages
     */
    public function remove(array $packages, Env $env = Env::Production, ?Provider $provider = null): void
    {
        $this->createImportMap($env, $provider, false, [], $packages);
    }

    /**
     * Updates all existing packages to the latest version.
     */
    public function update(Env $env = Env::Production, ?Provider $provider = null): void
    {
        $this->createImportMap($env, $provider, true, [], []);
    }

    private function createImportMap(Env $env, ?Provider $provider, bool $update, array $require, array $remove): void
    {
        $this->loadImportMap();

        $install = [];
        foreach ($this->importMap['imports'] ?? [] as $url) {
            if (preg_match(self::PACKAGE_PATTERN, $url, $matches)) {
                $constraint = ($matches['registry'] ?? null) ? "{$matches['registry']}:{$matches['package']}" : $matches['package'];

                if (!$update && ($matches['version'] ?? null)) {
                    $constraint .= "@{$matches['version']}";
                }

                $install[$matches['package']] = $constraint;
            }
        }

        foreach ($remove as $package) {
            if (preg_match(self::PACKAGE_PATTERN, $package, $matches)) {
                unset($install[$package]);
            }
        }

        foreach ($require as $package) {
            if (preg_match(self::PACKAGE_PATTERN, $package, $matches)) {
                $install[$matches['package']] = $package;
            }
        }

        $json = [
            'install' => array_values($install),
            'flattenScope' => true,
            'provider' => $provider?->value ?? $this->provider->value,
        ];

        $json['env'] = ['browser', 'module', $env->value];

        $response = $this->httpClient->request('POST', '/generate', [
            'json' => $json,
        ]);

        $this->importMap = $response->toArray()['map'];

        file_put_contents($this->path, sprintf("<?php\n\nreturn %s;\n", VarExporter::export($this->importMap)));
    }
}
