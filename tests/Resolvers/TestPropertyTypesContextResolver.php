<?php

use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Tests\TestTypeDoc\TypeOneDoc;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;

beforeEach(function () {
    /** @var PropertyTypesContextResolver $this */
    $this->propertyTypesContextResolver = new PropertyTypesContextResolver();
});

it('tests resolve type from doc block', function () {
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'data_doc');
    $result             = $this->propertyTypesContextResolver->resolveTypeFromDocBlock($reflectionProperty);
    expect($result)->toBeInstanceOf(Object_::class)
        ->and($result->getFqsen())->toBeInstanceOf(Fqsen::class)
        ->and($result->getFqsen()->__toString())->toBe('Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc')
        ->and($result->getFqsen()->getName())->toBe('BothTypeDoc');
});
