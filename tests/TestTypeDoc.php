<?php

declare(strict_types=1);

namespace Astral\Serialize\Tests;

use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Astral\Serialize\Annotations\Groups;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use Astral\Serialize\Annotations\PropertyAlisa;
use Astral\Serialize\Tests\TestRequest\TypeDoc;
use Astral\Serialize\Annotations\PropertyAlisaByGroup;
use phpDocumentor\Reflection\Types\Context;

class TestTypeDoc extends TestCase
{

    /**
     * 测试 `acreages` 属性的反射和类型解析
     */
    public function testTypeDoc(): void
    {
        // 使用反射获取 `acreages` 属性
        $reflection = new ReflectionProperty(TypeDoc::class, 'vols');

        // 获取 PHPDoc 注释
        $docComment = $reflection->getDocComment();
        $this->assertNotFalse($docComment, 'PHPDoc 注释不存在');




        $namespace = (new \phpDocumentor\Reflection\Types\ContextFactory())->createFromReflector($reflection->getDeclaringClass());

        // var_dump($namespace);

        // // $project = $projectFactory->create('MyProject', $files);

        // foreach ($project->getFiles()['tests/TestRequest/TypeDoc.php']->getClasses() as $class) {
        //     echo '- ' . $class->getFqsen() . PHP_EOL;
        // }

        // die;


        // 创建 DocBlockFactory 实例
        $docFactory = DocBlockFactory::createInstance();
        $docBlock = $docFactory->create($docComment);

        // 获取 @var 标签
        $tags = $docBlock->getTagsByName('var');
        $this->assertNotEmpty($tags, '@var 标签不存在');

        // 创建解析上下文
        // $namespace = 'Astral\Serialize\Tests\TestRequest';
        // $context = new Context($namespace);

        // 使用 TypeResolver 解析类型
        $typeResolver = new TypeResolver();
        $type = $typeResolver->resolve((string)$tags[0]->getType(), $namespace);

        $this->assertStringContainsString('Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc', (string)$type, '解析的类型中未包含 OtherTypeDoc');

        // 可选：验证类型是否解析为数组形式
        $this->assertStringContainsString('[]', (string)$type, '解析的类型未标识为数组');
    }
}
