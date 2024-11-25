<?php

use ReflectionProperty;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;

beforeEach(function () {
    /** @var PropertyTypesContextResolver */
    $this->propertyTypesContextResolver = new PropertyTypesContextResolver();
});

it('tests resolve type from doc block', function () {
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'data_doc');
    $result             = $this->propertyTypesContextResolver->resolveTypeFromDocBlock($reflectionProperty);
    expect($result)->toBeInstanceOf(Object_::class);
    expect($result->getFqsen())->toBeInstanceOf(Fqsen::class);
    expect($result->getFqsen()->__toString())->toBe('Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc');
    expect($result->getFqsen()->getName())->toBe('BothTypeDoc');
});
