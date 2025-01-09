<?php

namespace Astral\Benchmarks\Fake;

class NestedCollectionFake
{
    public function __construct(
        public readonly int $int,
        public readonly string $string,
        /** @var NestedDataFake[] */
        public readonly array $nestedData,
    ) {

    }
}
