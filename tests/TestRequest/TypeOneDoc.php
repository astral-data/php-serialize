<?php

namespace Astral\Serialize\Tests\TestRequest;

use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;
use Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\Other\ReqOtherEnum;

class TypeOneDoc
{
    #[InputName('input_name')]
    #[OutIgnore]
    /** @var OtherTypeDoc[] */
    public array $type_collect_object;

    /** @var BothTypeDoc */
    public object $type_class_object_doc;

    public OtherTypeDoc $type_class_object;

    public object $type_object;

    public string $type_string;

    public float $type_float;

    public bool $type_bool;

    public int $type_int;

    public ?int $default_value = 1;

    public ReqEnum $type_enum;

    public ReqOtherEnum $type_enum_1;
}
