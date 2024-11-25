<?php

namespace Astral\Serialize\Tests\TestRequest;

use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc;

class TypeUnionDoc
{
    /** @var OtherTypeDoc[]|BothTypeDoc[] */
    public array $union_vols;

    /** @var OtherTypeDoc|BothTypeDoc[] */
    public object|array $union_data_doc;

    public OtherTypeDoc|BothTypeDoc|string $union_data;
}
