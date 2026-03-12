<?php

declare(strict_types=1);

namespace Monitaroo\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MonitarooBundle integrates Monitaroo SDK with Symfony.
 */
class MonitarooBundle extends Bundle
{
    /**
     * @return string
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
