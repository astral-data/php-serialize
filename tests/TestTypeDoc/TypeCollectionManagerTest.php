<?php

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Resolvers\PropertyTypeDocResolver;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Collections\Manager\TypeCollectionManager;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Tests\TestTypeDoc\TypeOneDoc;
use Astral\Serialize\Tests\TestTypeDoc\TypeUnionDoc;
use phpDocumentor\Reflection\TypeResolver;

beforeEach(function () {
    /** @var TypeCollectionManager $this */
    $this->typeManager = new TypeCollectionManager(new PropertyTypeDocResolver(), new PropertyTypesContextResolver(), new TypeResolver());
});

it('tests one property reflections not parsing doc', function () {
    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_collect_object');

    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::ARRAY)
        ->and($result->className)->toBeNull();

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_class_object_doc');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::MIXED)
        ->and($result->className)->toBeNull();
});

it('tests one property reflections and type parsing', function () {

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_class_object');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::CLASS_OBJECT)
        ->and($result->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc');


    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_object');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::OBJECT)
        ->and($result->className)->toBeNull();


    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_string');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::STRING)
        ->and($result->className)->toBeNull();


    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_float');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::FLOAT)
        ->and($result->className)->toBeNull();

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_int');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::INT)
        ->and($result->className)->toBeNull();

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_bool');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::BOOLEAN)
        ->and($result->className)->toBeNull();

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_enum_1');
    $result             = $this->typeManager->processNamedType($reflectionProperty->getType(), $reflectionProperty);
    expect($result)->toBeInstanceOf(TypeCollection::class)
        ->and($result->kind)->toBe(TypeKindEnum::ENUM)
        ->and($result->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\ReqOtherEnum');
});

it('tests union property reflections and type parsing', function () {
    $reflectionProperty = new ReflectionProperty(TypeUnionDoc::class, 'union_data');
    $result             = $this->typeManager->processUnionType($reflectionProperty->getType(), $reflectionProperty);


    expect($result)->toBeArray()->toHaveCount(3);

    foreach ($result as $key => $item) {
        expect($item)->toBeInstanceOf(TypeCollection::class);
        match ($key) {
            0 => expect($item->kind)->toBe(TypeKindEnum::CLASS_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc'),
            1 => expect($item->kind)->toBe(TypeKindEnum::CLASS_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc'),
            2 => expect($item->kind)->toBe(TypeKindEnum::STRING)
                ->and($item->className)->toBeNull(),
            default => expect(false)->toBeTrue("Unexpected element at index {$key}")
        };
    }
});

it('tests union doc property reflections and type parsing', function () {

    $reflectionProperty = new ReflectionProperty(TypeUnionDoc::class, 'mixed_array');
    $result             = $this->typeManager->getCollectionTo($reflectionProperty);
    expect($result)->toBeArray()->toHaveCount(2);
    foreach ($result as $key => $item) {
        expect($item)->toBeInstanceOf(TypeCollection::class);
        match ($key) {
            0 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_UNION_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc'),
            1 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_UNION_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc'),
            default => expect(false)->toBeTrue("Unexpected element at index {$key}")
        };
    }


    $reflectionProperty = new ReflectionProperty(TypeUnionDoc::class, 'union_data_doc');
    $result             = $this->typeManager->getCollectionTo($reflectionProperty);

    expect($result)->toBeArray()->toHaveCount(4);
    foreach ($result as $key => $item) {
        expect($item)->toBeInstanceOf(TypeCollection::class);
        match ($key) {
            0 => expect($item->kind)->toBe(TypeKindEnum::CLASS_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc'),
            1 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_SINGLE_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc'),
            2 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_UNION_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc'),
            3 => expect($item->kind)->toBe(TypeKindEnum::COLLECT_UNION_OBJECT)
                ->and($item->className)->toBe('Astral\Serialize\Tests\TestTypeDoc\Both\BothTypeDoc'),
            default => expect(false)->toBeTrue("Unexpected element at index $key")
        };
    }

    $reflectionProperty = new ReflectionProperty(TypeOneDoc::class, 'type_class_object_doc');
    $result             = $this->typeManager->getCollectionTo($reflectionProperty);

    expect($result)->toBeArray()->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(Astral\Serialize\Support\Collections\TypeCollection::class)
        ->and($result[0]->kind)->toBe(Astral\Serialize\Enums\TypeKindEnum::MIXED)
        ->and($result[0]->className)->toBeNull();
});
