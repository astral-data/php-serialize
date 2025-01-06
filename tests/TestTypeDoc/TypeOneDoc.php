<?php

namespace Astral\Serialize\Tests\TestTypeDoc;

use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;
use Astral\Serialize\Serialize;
use Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc;
use Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestTypeDoc\Other\ReqOtherEnum;

class TypeOneDoc extends Serialize
{
    #[InputName('input_name')]
    #[OutIgnore]
    /** @var OtherTypeDoc[] $type_collect_object */
    public array|object $type_collect_object;

    /** @var BothTypeDoc $type_class_object_doc */
    public object|string $type_class_object_doc;

    public OtherTypeDoc $type_class_object;

    public object $type_object;

    public ?string $type_string;

    public float $type_float;

    #[InputName('type_mixed_other.abc')]
    public readonly mixed $type_mixed;

    public bool $type_bool;

    public ?int $type_int;

    public ?int $default_value = 1;

    public ReqEnum $type_enum;

    public ReqOtherEnum $type_enum_1;
}
