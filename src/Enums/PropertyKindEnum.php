<?php

namespace Astral\Serialize\Enums;


enum PropertyKindEnum
{
    case STRING;
    case INT;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
    case CollectObject;
    case ENUM;
    case DATE;


}
