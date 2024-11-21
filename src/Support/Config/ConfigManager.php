<?php

namespace Astral\Serialize\Support\Config;

class ConfigManager {
    public static ConfigManager $instance;
    private array $inputTransFrom = [];
    private array $outTransFrom = [];

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    public function addInputTransFrom($val):static
    {
        $this->inputTransFrom[] = $val;
        return $this;
    }

    public function addOutTransFrom($val) :static
    {
        $this->outTransFrom[] = $val;
        return $this;
    }

    public function getInputTransFrom(): array
    {
        return $this->inputTransFrom;
    }

    public  function getOutTransFrom(): array
    {
        return $this->outTransFrom;
    }
}