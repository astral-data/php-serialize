<?php

namespace Astral\Serialize\OpenApi\Enum;

enum ParameterTypeEnum: string
{
    case ARRAY  = 'array';
    case STRING = 'string';
    case OBJECT = 'object';
}
