<?php

declare(strict_types=1);

namespace Test\App\Twig\Extension;

use acalvino4\embed\Extension;
use Twig\Test\IntegrationTestCase;

final class IntegrationTest extends IntegrationTestCase
{
    protected function getFixturesDir(): string
    {
        return __DIR__ . "/fixtures/";
    }

    protected function getExtensions(): iterable
    {
        yield new Extension();
    }
}
