<?php

namespace Astral\Serialize\Tests\TestRequest;

use Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;

class TypeDoc
{
    /** @var OtherTypeDoc[]|BothTypeDoc[]|array<string,BothTypeDoc>|string[] */
    public array $vols;
}
