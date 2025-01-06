<?php

namespace Astral\Serialize\Tests\TestTypeDoc;

use Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc;

class TypeUnionDoc
{
    /** @var OtherTypeDoc[]|BothTypeDoc[] */
    public array $union_vols;

    /** @var OtherTypeDoc|BothTypeDoc[] */
    public object|array $union_data_doc;

    public OtherTypeDoc|BothTypeDoc|string $union_data;
}
