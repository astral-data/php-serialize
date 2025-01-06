<?php

namespace Astral\Serialize\Tests\TestTypeDoc;

use Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc;

class TypeDoc
{
    /** @var OtherTypeDoc[]|BothTypeDoc[]|array<string,BothTypeDoc>|string[] */
    public array $vols;
}
