<?php

declare(strict_types=1);

namespace Astral\Serialize\Tests;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Astral\Serialize\Annotations\Groups;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use Astral\Serialize\Annotations\PropertyAlisa;
use Astral\Serialize\Tests\TestRequest\TypeDoc;
use phpDocumentor\Reflection\Types\Context;

class TestTypeDoc extends TestCase
{
    
    public function testTypeDoc(): void
    {
        // 使用反射获取 `acreages` 属性
        $reflectionProperty = new ReflectionProperty(TypeDoc::class, 'vols');

        // 获取 PHPDoc 注释
        $docComment = $reflectionProperty->getDocComment();
        $this->assertNotFalse($docComment, 'PHPDoc 注释不存在');

        $context = (new ContextFactory())->createFromReflector($reflectionProperty->getDeclaringClass());

        $docFactory = DocBlockFactory::createInstance();
        $docBlock = $docFactory->create($docComment,$context);

        /** @var Var_|null $typeDocs */
        $typeDocs = $docBlock->getTagsByName('var')[0] ?? null;
        $this->assertNotEmpty($typeDocs, '@var 标签不存在');

        $types = $typeDocs->getType();

        if($types instanceof Compound){
            foreach ($types as $singleType){
                print_r($singleType);
            }
        }
//        var_dump($typeDocs);
        die;

        // 使用 TypeResolver 解析类型
        $typeResolver = new TypeResolver();
//        $type = $typeResolver->resolve($varDocs[0]->__toString(), $context);

        $type = $typeResolver->resolve('(OtherTypeDoc|BothTypeDoc)[]', $context);

        var_dump( $context,$type->__toString());
        die;

        $this->assertStringContainsString('Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc', (string)$type, '解析的类型中未包含 OtherTypeDoc');

        // 可选：验证类型是否解析为数组形式
        $this->assertStringContainsString('[]', (string)$type, '解析的类型未标识为数组');
    }
}
