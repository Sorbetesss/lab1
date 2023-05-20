<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\AssetMapper\Tests\ImportMap;

use PHPUnit\Framework\TestCase;
use Symfony\Component\AssetMapper\AssetMapper;
use Symfony\Component\AssetMapper\AssetMapperCompiler;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\AssetMapperRepository;
use Symfony\Component\AssetMapper\Compiler\JavaScriptImportPathCompiler;
use Symfony\Component\AssetMapper\Factory\MappedAssetFactory;
use Symfony\Component\AssetMapper\ImportMap\ImportMapManager;
use Symfony\Component\AssetMapper\ImportMap\PackageRequireOptions;
use Symfony\Component\AssetMapper\Path\PublicAssetsPathResolver;
use Symfony\Component\AssetMapper\Path\PublicAssetsPathResolverInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ImportMapManagerTest extends TestCase
{
    private MockHttpClient $httpClient;
    private Filesystem $filesystem;
    private AssetMapperInterface $assetMapper;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        if (!file_exists(__DIR__.'/../fixtures/importmaps_for_writing')) {
            $this->filesystem->mkdir(__DIR__.'/../fixtures/importmaps_for_writing');
        }
        if (!file_exists(__DIR__.'/../fixtures/importmaps_for_writing/assets')) {
            $this->filesystem->mkdir(__DIR__.'/../fixtures/importmaps_for_writing/assets');
        }
        file_put_contents(__DIR__.'/../fixtures/importmaps_for_writing/assets/some_file.js', '// some_file.js contents');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(__DIR__.'/../fixtures/importmaps_for_writing');
    }

    public function testGetModulesToPreload()
    {
        $manager = $this->createImportMapManager(
            ['assets' => '', 'assets2' => 'namespaced_assets2'],
            __DIR__.'/../fixtures/importmaps/'
        );
        $this->assertEquals([
            'https://unpkg.com/@hotwired/stimulus@3.2.1/dist/stimulus.js',
            '/assets/app-ea9ebe6156adc038aba53164e2be0867.js',
            // these are non-lazily imported from app.js
            '/assets/pizza/index-b3fb5ee31adaf5e1b32d28edf1ab8e7a.js',
            '/assets/popcorn-c0778b84ef9893592385aebc95a2896e.js',
        ], $manager->getModulesToPreload());
    }

    public function testGetImportMapJson()
    {
        $manager = $this->createImportMapManager(
            ['assets' => '', 'assets2' => 'namespaced_assets2'],
            __DIR__.'/../fixtures/importmaps/'
        );
        $this->assertEquals(['imports' => [
            '@hotwired/stimulus' => 'https://unpkg.com/@hotwired/stimulus@3.2.1/dist/stimulus.js',
            'lodash' => '/assets/vendor/lodash-ad7bd7bf42edd09654255a82b9027810.js',
            'app' => '/assets/app-ea9ebe6156adc038aba53164e2be0867.js',
            '/assets/pizza/index.js' => '/assets/pizza/index-b3fb5ee31adaf5e1b32d28edf1ab8e7a.js',
            '/assets/popcorn.js' => '/assets/popcorn-c0778b84ef9893592385aebc95a2896e.js',
            '/assets/imported_async.js' => '/assets/imported_async-8f0cd418bfeb0cf63826e09a4474a81c.js',
            'other_app' => '/assets/namespaced_assets2/app2-d5bf10c20bf9a0b77e67d78fcac301c5.js',
            '/assets/namespaced_assets2/imported.js' => '/assets/namespaced_assets2/imported-9ab37dabcfe317fba77123a4e573d53b.js',
        ]], json_decode($manager->getImportMapJson(), true));
    }

    public function testGetImportMapJsonUsesDumpedFile()
    {
        $manager = $this->createImportMapManager(
            ['assets' => ''],
            __DIR__.'/../fixtures/',
            '/final-assets',
            'test_public'
        );
        $this->assertEquals(['imports' => [
            '@hotwired/stimulus' => 'https://unpkg.com/@hotwired/stimulus@3.2.1/dist/stimulus.js',
            'app' => '/assets/app-ea9ebe6156adc038aba53164e2be0867.js',
        ]], json_decode($manager->getImportMapJson(), true));
        $this->assertEquals([
            '/assets/app-ea9ebe6156adc038aba53164e2be0867.js',
        ], $manager->getModulesToPreload());
    }

    /**
     * @dataProvider getRequirePackageTests
     */
    public function testRequire(array $packages, array $expectedInstallRequest, array $responseMap, array $expectedImportMap, array $expectedDownloadedFiles)
    {
        $rootDir = __DIR__.'/../fixtures/importmaps_for_writing';
        $manager = $this->createImportMapManager(['assets' => ''], $rootDir);

        $expectedRequestBody = [
            'install' => $expectedInstallRequest,
            'flattenScope' => true,
            'env' => ['browser', 'module', 'production'],
        ];
        $responseData = [
            'map' => [
                'imports' => $responseMap,
            ],
        ];
        $responses = [];
        $responses[] = function ($method, $url, $options) use ($responseData, $expectedRequestBody) {
            $this->assertSame('POST', $method);
            $this->assertSame('https://example.com/generate', $url);
            $this->assertSame($expectedRequestBody, json_decode($options['body'], true));

            return new MockResponse(json_encode($responseData));
        };
        // mock the "file download" requests
        foreach ($expectedDownloadedFiles as $file) {
            $responses[] = new MockResponse(sprintf('contents of %s', $file));
        }
        $this->httpClient->setResponseFactory($responses);

        $manager->require($packages);
        $actualImportMap = require $rootDir.'/importmap.php';
        $this->assertEquals($expectedImportMap, $actualImportMap);
        foreach ($expectedDownloadedFiles as $file) {
            $this->assertFileExists($rootDir.'/'.$file);
            $actualContents = file_get_contents($rootDir.'/'.$file);
            $this->assertSame(sprintf('contents of %s', $file), $actualContents);
        }
    }

    public static function getRequirePackageTests(): iterable
    {
        yield 'require single lodash package' => [
            'packages' => [new PackageRequireOptions('lodash')],
            'expectedInstallRequest' => ['lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'require two packages' => [
            'packages' => [new PackageRequireOptions('lodash'), new PackageRequireOptions('cowsay')],
            'expectedInstallRequest' => ['lodash', 'cowsay'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                'cowsay' => 'https://ga.jspm.io/npm:cowsay@4.5.6/cowsay.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                ],
                'cowsay' => [
                    'url' => 'https://ga.jspm.io/npm:cowsay@4.5.6/cowsay.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_that_returns_as_two' => [
            'packages' => [new PackageRequireOptions('lodash')],
            'expectedInstallRequest' => ['lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                'lodash-dependency' => 'https://ga.jspm.io/npm:lodash-dependency@9.8.7/lodash-dependency.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                ],
                'lodash-dependency' => [
                    'url' => 'https://ga.jspm.io/npm:lodash-dependency@9.8.7/lodash-dependency.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_with_version_constraint' => [
            'packages' => [new PackageRequireOptions('lodash', '^1.2.3')],
            'expectedInstallRequest' => ['lodash@^1.2.3'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.7/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.7/lodash.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_that_downloads' => [
            'packages' => [new PackageRequireOptions('lodash', download: true)],
            'expectedInstallRequest' => ['lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                    'downloaded_to' => 'vendor/lodash.js',
                ],
            ],
            'expectedDownloadedFiles' => [
                'assets/vendor/lodash.js',
            ],
        ];

        yield 'single_package_that_preloads' => [
            'packages' => [new PackageRequireOptions('lodash', preload: true)],
            'expectedInstallRequest' => ['lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                    'preload' => true,
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_with_custom_import_name' => [
            'packages' => [new PackageRequireOptions('lodash', importName: 'lodash-es')],
            'expectedInstallRequest' => ['lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash-es' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_with_jspm_custom_registry' => [
            'packages' => [new PackageRequireOptions('lodash', registryName: 'jspm')],
            'expectedInstallRequest' => ['jspm:lodash'],
            'responseMap' => [
                'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'expectedImportMap' => [
                'lodash' => [
                    'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];

        yield 'single_package_with_a_path' => [
            'packages' => [new PackageRequireOptions('some/module', path: __DIR__.'/../fixtures/importmaps_for_writing/assets/some_file.js')],
            'expectedInstallRequest' => [],
            'responseMap' => [],
            'expectedImportMap' => [
                'some/module' => [
                    'path' => 'some_file.js',
                ],
            ],
            'expectedDownloadedFiles' => [],
        ];
    }

    public function testRemove()
    {
        $rootDir = __DIR__.'/../fixtures/importmaps_for_writing';
        $manager = $this->createImportMapManager(['assets' => ''], $rootDir);

        $map = [
            'lodash' => [
                'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'cowsay' => [
                'url' => 'https://ga.jspm.io/npm:cowsay@4.5.6/cowsay.umd.js',
                'downloaded_to' => 'vendor/moo.js',
            ],
            'chance' => [
                'url' => 'https://ga.jspm.io/npm:chance@7.8.9/build/chance.js',
                'downloaded_to' => 'vendor/chance.js',
            ],
            'app' => [
                'path' => 'app.js',
            ],
            'other' => [
                'path' => 'other.js',
            ],
        ];
        $mapString = var_export($map, true);
        file_put_contents($rootDir.'/importmap.php', "<?php\n\nreturn {$mapString};\n");
        $this->filesystem->mkdir($rootDir.'/assets/vendor');
        touch($rootDir.'/assets/vendor/moo.js');
        touch($rootDir.'/assets/vendor/chance.js');
        touch($rootDir.'/assets/app.js');
        touch($rootDir.'/assets/other.js');

        $manager->remove(['cowsay', 'app']);
        $actualImportMap = require $rootDir.'/importmap.php';
        $expectedImportMap = $map;
        unset($expectedImportMap['cowsay'], $expectedImportMap['app']);
        $this->assertEquals($expectedImportMap, $actualImportMap);
        $this->assertFileDoesNotExist($rootDir.'/assets/vendor/moo.js');
        $this->assertFileDoesNotExist($rootDir.'/assets/app.js');
        $this->assertFileExists($rootDir.'/assets/vendor/chance.js');
        $this->assertFileExists($rootDir.'/assets/other.js');
    }

    public function testUpdate()
    {
        $rootDir = __DIR__.'/../fixtures/importmaps_for_writing';
        $manager = $this->createImportMapManager(['assets' => ''], $rootDir);

        $map = [
            'lodash' => [
                'url' => 'https://ga.jspm.io/npm:lodash@1.2.3/lodash.js',
            ],
            'cowsay' => [
                'url' => 'https://ga.jspm.io/npm:cowsay@4.5.6/cowsay.umd.js',
                'downloaded_to' => 'vendor/moo.js',
            ],
            'canvas-confetti' => [
                'url' => 'https://cdn.skypack.dev/pin/canvas-confetti@v1.5.0-t438JJTXIbBReqvLtDua/mode=imports,min/optimized/canvas-confetti.js',
                'preload' => true,
            ],
            'app' => [
                'path' => 'app.js',
            ],
        ];
        $mapString = var_export($map, true);
        file_put_contents($rootDir.'/importmap.php', "<?php\n\nreturn {$mapString};\n");
        $this->filesystem->mkdir($rootDir.'/assets/vendor');
        file_put_contents($rootDir.'/assets/vendor/moo.js', 'moo.js contents');
        file_put_contents($rootDir.'/assets/app.js', 'app.js contents');

        $responses = [];
        $responses[] = function ($method, $url, $options) {
            $this->assertSame('POST', $method);
            $this->assertSame('https://example.com/generate', $url);

            return new MockResponse(json_encode([
                'map' => [
                    'imports' => [
                        'lodash' => 'https://ga.jspm.io/npm:lodash@1.2.9/lodash.js',
                        'cowsay' => 'https://ga.jspm.io/npm:cowsay@4.5.9/cowsay.umd.js',
                        'canvas-confetti' => 'https://cdn.skypack.dev/pin/canvas-confetti@v1.6.0-t438JJTXIbBReqvLtDua/mode=imports,min/optimized/canvas-confetti.js',
                    ],
                ],
            ]));
        };
        // 1 file will be downloaded
        $responses[] = new MockResponse('contents of cowsay.js');
        $this->httpClient->setResponseFactory($responses);

        $manager->update();
        $actualImportMap = require $rootDir.'/importmap.php';
        $expectedImportMap = [
            'lodash' => [
                'url' => 'https://ga.jspm.io/npm:lodash@1.2.9/lodash.js',
            ],
            'cowsay' => [
                'url' => 'https://ga.jspm.io/npm:cowsay@4.5.9/cowsay.umd.js',
                'downloaded_to' => 'vendor/cowsay.js',
            ],
            // a non-jspm URL so we can make sure it updates
            'canvas-confetti' => [
                'url' => 'https://cdn.skypack.dev/pin/canvas-confetti@v1.6.0-t438JJTXIbBReqvLtDua/mode=imports,min/optimized/canvas-confetti.js',
                'preload' => true,
            ],
            'app' => [
                'path' => 'app.js',
            ],
        ];
        $this->assertEquals($expectedImportMap, $actualImportMap);
        $this->assertFileDoesNotExist($rootDir.'/assets/vendor/moo.js');
        $this->assertFileExists($rootDir.'/assets/vendor/cowsay.js');
        $actualContents = file_get_contents($rootDir.'/assets/vendor/cowsay.js');
        $this->assertSame('contents of cowsay.js', $actualContents);
    }

    /**
     * @dataProvider getPackageNameTests
     */
    public function testParsePackageName(string $packageName, array $expectedReturn)
    {
        $parsed = ImportMapManager::parsePackageName($packageName);
        // remove integer keys - they're noise

        if (\is_array($parsed)) {
            $parsed = array_filter($parsed, function ($key) {
                return !\is_int($key);
            }, \ARRAY_FILTER_USE_KEY);
        }
        $this->assertEquals($expectedReturn, $parsed);
    }

    public static function getPackageNameTests(): iterable
    {
        yield 'simple' => [
            'lodash',
            [
                'package' => 'lodash',
                'registry' => '',
            ],
        ];

        yield 'with_version_constraint' => [
            'lodash@^1.2.3',
            [
                'package' => 'lodash',
                'registry' => '',
                'version' => '^1.2.3',
            ],
        ];

        yield 'with_registry' => [
            'npm:lodash',
            [
                'package' => 'lodash',
                'registry' => 'npm',
            ],
        ];

        yield 'with_registry_and_version' => [
            'npm:lodash@^1.2.3',
            [
                'package' => 'lodash',
                'registry' => 'npm',
                'version' => '^1.2.3',
            ],
        ];

        yield 'namespaced_package_simple' => [
            '@hotwired/stimulus',
            [
                'package' => '@hotwired/stimulus',
                'registry' => '',
            ],
        ];

        yield 'namespaced_package_with_version_constraint' => [
            '@hotwired/stimulus@^1.2.3',
            [
                'package' => '@hotwired/stimulus',
                'registry' => '',
                'version' => '^1.2.3',
            ],
        ];

        yield 'namespaced_package_with_registry' => [
            'npm:@hotwired/stimulus',
            [
                'package' => '@hotwired/stimulus',
                'registry' => 'npm',
            ],
        ];

        yield 'namespaced_package_with_registry_and_version' => [
            'npm:@hotwired/stimulus@^1.2.3',
            [
                'package' => '@hotwired/stimulus',
                'registry' => 'npm',
                'version' => '^1.2.3',
            ],
        ];
    }

    private function createImportMapManager(array $dirs, string $rootDir, string $publicPrefix = '/assets/', string $publicDirName = 'public'): ImportMapManager
    {
        $pathResolver = new PublicAssetsPathResolver($rootDir, $publicPrefix, $publicDirName);

        $mapper = $this->createAssetMapper($pathResolver, $dirs, $rootDir);
        $this->httpClient = new MockHttpClient();

        return new ImportMapManager(
            $mapper,
            $pathResolver,
            $rootDir.'/importmap.php',
            $rootDir.'/assets/vendor',
            ImportMapManager::PROVIDER_JSPM,
            $this->httpClient
        );
    }

    private function createAssetMapper(PublicAssetsPathResolverInterface $pathResolver, array $dirs, string $rootDir): AssetMapper
    {
        $repository = new AssetMapperRepository($dirs, $rootDir);

        $compiler = new AssetMapperCompiler(
            [new JavaScriptImportPathCompiler()],
            fn () => $this->assetMapper
        );
        $factory = new MappedAssetFactory($pathResolver, $compiler);

        $this->assetMapper = new AssetMapper(
            $repository,
            $factory,
            $pathResolver
        );

        return $this->assetMapper;
    }
}
