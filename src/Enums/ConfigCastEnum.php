<?php

namespace Astral\Serialize\Enums;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;

enum ConfigCastEnum: string
{
    case PROPERTY         = 'attributePropertyResolver';
    case OUTPUT_VALUE     = 'outputValueCasts';
    case INPUT_VALUE      = 'inputValueCasts';
    case INPUT_NORMALIZER = 'inputNormalizerCasts';
    case OUT_NORMALIZER   = 'outNormalizerCasts';

    public function getCastInterface(): string
    {
        return match ($this) {
            self::PROPERTY     => DataCollectionCastInterface::class,
            self::OUTPUT_VALUE => OutValueCastInterface::class,
            self::INPUT_VALUE  => InputValueCastInterface::class,
            self::INPUT_NORMALIZER, self::OUT_NORMALIZER => NormalizerCastInterface::class,
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
