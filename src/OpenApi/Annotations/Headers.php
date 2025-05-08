<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Headers
{
    /**
     * @param array $headers
     * @param array $withOutHeaders
     * @example
     *   headers: [['company-id'=>'1'],['test-header'=>'test']]
     *   withOutHeaders: ['test-header','company-id']
     */
    public function __construct(
        public array $headers = [],
        public array $withOutHeaders = []
    ) {
    }
}
