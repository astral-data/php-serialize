<?php

declare(strict_types=1);

namespace Asrtal\Serialize\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class InputDataFormat
{
    /** @var string input-format */
    public string $inputFormat;

    /** @var string out-format */
    public string $outFormat;

    /**
     * @param  string  $inputFormat  
     * @param  string  $outFormat 
     */
    public function __construct(string $inputFormat = 'Y-m-d H:i:s', string $outFormat = 'Y-m-d H:i:s')
    {
        $this->inputFormat = $inputFormat;
        $this->outFormat = $outFormat;
    }
}
