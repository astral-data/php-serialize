<?php

namespace Astral\Serialize;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

class SerializeContainer {

    protected static self $instance;
    protected ?ContextFactory $contextFactory = null;
    protected ?TypeResolver $typeResolver = null;
    protected ?DocBlockFactory $docBlockFactory = null;

    public static function get(): SerializeContainer
    {
        return static::$instance ??= new self();
    }

    public function contextFactory() :ContextFactory
    {
        return $this->contextFactory ??= new ContextFactory();
    }

    public function typeResolver() : TypeResolver
    {
        return $this->typeResolver??= new TypeResolver();
    }

    public function docBlockFactory() : DocBlockFactory
    {
        return $this->docBlockFactory ??= DocBlockFactory::createInstance();
    }

}