<?php

declare(strict_types=1);

namespace Darkweak\SouinApiPlatformBundle\Tests;

use Darkweak\SouinApiPlatformBundle\SouinApiPlatformBundle;
use PHPUnit\Framework\TestCase;

/**
 * Class SouinApiPlatformBundleTest
 *
 * @package UnitTests\TranslationBundle
 * @covers \Darkweak\SouinApiPlatformBundle\SouinApiPlatformBundle
 */
class SouinApiPlatformBundleTest extends TestCase
{
    public function testClassExist(): void
    {
        $this->assertTrue(class_exists(SouinApiPlatformBundle::class));
    }

    public function testExtensionIsLoaded(): void
    {
        $bundle = new SouinApiPlatformBundle();
        $this->assertNotNull($bundle->getContainerExtension());
    }
}
