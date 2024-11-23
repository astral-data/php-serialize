<?php

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Collections\TypeCollectionManager;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;
use Astral\Serialize\Tests\TestRequest\TypeUnionDoc;
use phpDocumentor\Reflection\TypeResolver;

beforeEach(function () {
    /** @var TypeCollectionManager */
    $this->typeManager = new TypeCollectionManager(new PropertyTypesContextResolver(), new TypeResolver());
});

it('tests one property reflections and type parsing', function () {
    // Test vols property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'vols');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::ARRAY)
        ->and($result->className)->toBeNull();

    // Test data property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'data');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::OBJECT)
        ->and($result->className)->toBe('Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc');

    // Test type_string property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_string');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::STRING)
        ->and($result->className)->toBeNull();

    // Test type_float property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_float');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::FLOAT)
        ->and($result->className)->toBeNull();

    // Test type_bool property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_bool');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::BOOLEAN)
        ->and($result->className)->toBeNull();

    // Test type_enum_1 property
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_enum_1');
    $result = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::ENUM)
        ->and($result->className)->toBe('Astral\Serialize\Tests\TestRequest\Other\ReqOtherEnum');
});

it('tests union property reflections and type parsing', function () {
    $reflectionProperty = new ReflectionProperty(TypeUnionDoc::class, 'union_data');
    $result = $this->typeManager->processUnionType($reflectionProperty->getType(), $reflectionProperty);

    expect($result)->toBeArray()->toHaveCount(3);

    foreach ($result as $key => $item) {
        expect($item)->toBeInstanceOf(TypeCollection::class);
        match ($key) {
            0 => expect($item->kind)->toBe(TypeKindEnum::OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc'),
            1 => expect($item->kind)->toBe(TypeKindEnum::OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc'),
            2 => expect($item->kind)->toBe(TypeKindEnum::STRING)
                ->and($item->className)->toBeNull(),
            default => expect(false)->toBeTrue("Unexpected element at index {$key}")
        };
    }
});

it('tests union doc property reflections and type parsing', function () {
    $reflectionProperty = new ReflectionProperty(TypeUnionDoc::class, 'union_data_doc');
    $result = $this->typeManager->getCollectionTo($reflectionProperty);


    expect($result)->toBeArray()->toHaveCount(2);

    foreach ($result as $key => $item) {
        expect($item)->toBeInstanceOf(TypeCollection::class);

        match ($key) {
            0 => expect($item->kind)->toBe(TypeKindEnum::OBJECT)
                ->and($item->className)->toBe('\Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc'),
            1 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_OBJECT)
                ->and($item->className)->toBe('\Astral\Serialize\Tests\TestRequest\Both\BothTypeDoc'),
            default => expect(false)->toBeTrue("Unexpected element at index {$key}")
        };
    }
});
