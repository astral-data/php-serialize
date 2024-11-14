<?php

namespace Asrtal\Serialize\Enums;


enum PropertyTypeEnum
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
