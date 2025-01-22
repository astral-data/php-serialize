<?php

namespace Astral\Serialize\Tests\Resolvers;

use Astral\Serialize\Resolvers\GroupResolver;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class GroupResolverTest extends TestCase
{
    private GroupResolver $groupResolver;
    private CacheInterface $cache;

    protected function setUp(): void
    {
        $this->cache         = $this->createMock(CacheInterface::class);
        $this->groupResolver = new GroupResolver($this->cache);
    }
}
