<?php

namespace Astral\Serialize\Tests\TestRequest;

use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestRequest\Other\ReqOtherEnum;

class TypeOneDoc
{

    /** @var OtherTypeDoc[] */
    public array $vols;

    /** @var BothTypeDoc */
    public object $data_doc;

    public OtherTypeDoc $data;

    public string $type_string;

    public float $type_float;

    public bool $type_bool;

    public ReqEnum $type_enum;

    public ReqOtherEnum $type_enum_1;
}
