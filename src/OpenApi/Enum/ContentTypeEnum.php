<?php

namespace Astral\Serialize\OpenApi\Enum;

enum ContentTypeEnum: string
{
    case FORM_DATA             = 'multipart/form-data';
    case X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    case JSON                  = 'application/json';
}
