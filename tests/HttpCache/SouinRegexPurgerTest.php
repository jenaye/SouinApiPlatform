<?php

/*
 * This file is not part of the API Platform project.
 * I won't copyright my work.
 */

declare(strict_types=1);

namespace Darkweak\SouinApiPlatformBundle\Tests;

use Darkweak\SouinApiPlatformBundle\HttpCache\SouinRegexPurger;
use PHPUnit\Framework\TestCase;

class SouinRegexPurgerTest extends TestCase {
    /** @var SouinRegexPurger */
    private $souinRegexPurgerMock;

    public function setUp(): void
    {
        $this->souinRegexPurgerMock = $this->prophesize(SouinRegexPurger::class)->reveal();
    }

    public function testPurge() {
        $this->souinRegexPurgerMock->purge([]);
        $this->souinRegexPurgerMock->purge(null);
        $this->souinRegexPurgerMock->purge(['/testings']);
    }
}

