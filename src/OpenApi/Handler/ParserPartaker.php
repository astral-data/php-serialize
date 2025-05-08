<?php

namespace Astral\Serialize\OpenApi\Handler;

use April\Serialize\Annotations\Group;
use Astral\Serialize\OpenApi\Storage\TreeNode;
use Exception;
use ReflectionClass;
use ReflectionProperty;

/**
 * 解析出入参数属性
 */
class ParserPartaker
{
    /** @var TreeNode */
    private $tree;

    /** @var array 忽略处理的关键词 */
    private $ignoreConst = ['int', 'float', 'bool', 'array', 'string', 'boolean', 'integer'];

    public function __construct()
    {
        $this->tree = new TreeNode();
    }

    public function addNode(string $className, ?string $group = null, ?TreeNode $childTree = null)
    {

        $classRefection = new ReflectionClass($className);

        foreach ($classRefection->getProperties() as $property) {

            if ($property->isProtected()) {
                continue;
            }

            if ($group && class_exists(Group::class)) {
                $attributes = $property->getAttributes(Group::class);
                if (! $attributes) {
                    continue;
                }

                $groups = $attributes[0]->newInstance();
                if (! in_array($group, $groups->names)) {
                    continue;
                }
            }

            $tree = new TreeNode($property);
            if ($childTree) {
                $childTree->addChildren($tree);
            } else {
                $this->tree->addChildren($tree);
            }

            // // debug
            // $docComment = <<<EOT
            // /**
            //  * @var CategoryDetailDto[] test
            //  * */
            // EOT;

            // $docComment2 = <<<EOT
            // /** @var CategoryDetailDto[] abc */
            // EOT;

            // $docComment3 = <<<EOT
            // /** @var \April\Generate\Responses\PaginateData */
            // EOT;

            // $docComment4 = <<<EOT
            // /**
            //  * @var \April\Generate\Responses\PaginateData 分页数据
            //  */
            // EOT;

            // $docComment5 = <<<EOT
            // /**
            //  * @var OrderByReq[] order_by
            //  *
            //  * @example [{"name":"id","sort":"DESC"}]
            //  */
            // EOT;

            // preg_match('/@var\s+([\\\\\w\[\]]+)(?:\s+(\S+))?\s*(.*?)(?:\n|\*\/)/s', $docComment, $varMatch);
            // preg_match('#@var[\s*](\S+)\s*(.*?)\n#', $docComment, $varMatch2);

            // preg_match('/@var\s+([\\\\\w\[\]]+)(?:\s+(\S+))?\s*(.*?)(?:\n|\*\/)/s', $docComment2, $varMatch3);
            // preg_match('#@var[\s*](\S+)\s*(.*?)\n#', $docComment2, $varMatch4);

            // preg_match('/@var\s+([\\\\\w\[\]]+)(?:\s+(\S+))?\s*(.*?)(?:\n|\*\/)/s', $docComment3, $varMatch5);
            // preg_match('#@var[\s*](\S+)\s*(.*?)\n#', $docComment3, $varMatch6);

            // preg_match('/@var\s+([\\\\\w\[\]]+)(?:\s+(\S+))?\s*(.*?)(?:\n|\*\/)/s', $docComment4, $varMatch7);
            // preg_match('#@var[\s*](\S+)\s*(.*?)\n#', $docComment4, $varMatch8);

            // preg_match('/@var\s+([\\\\\w\[\]]+)(?:\s+(\S+))?\s*(.*?)(?:\n|\*\/)/s', $docComment5, $varMatch9);
            // preg_match('#@var[\s*](\S+)\s*(.*?)\n#', $docComment5, $varMatch10);

            // dd($varMatch,$varMatch2,'---',$varMatch3,$varMatch4,'---',$varMatch5,$varMatch6,'---',$varMatch7,$varMatch8,'---',$varMatch9,$varMatch10);
            // // --end

            $docComment = $property->getDocComment();
            $varMatch   = getVarByDocComment($docComment);
            if (! $varMatch) {
                continue;
            }

            /**
             * var array<*,{string-class}>
             * var {string-class}[]
             * var {string-class}
             */
            if (
                ! preg_match('#array<\S+,(\S+)>#', $varMatch[1], $arrayMatch)
                && ! preg_match('#(\S+)\[\]#', $varMatch[1], $arrayMatch)
                && ! preg_match('#(\S+)#', $varMatch[1], $arrayMatch)
            ) {
                continue;
            }

            $listClass = trim($arrayMatch[1]);
            if (in_array(strtolower($listClass), $this->ignoreConst)) {
                continue;
            }

            try {
                $listClass = $this->getFullClassName($property, $listClass);
            } catch (\Throwable $th) {
                continue;
            }

            // 添加子级类信息
            if ($listClass != $className) {
                $this->addNode($listClass, $group, $tree);
            }
        }
    }

    public function getFullClassName(ReflectionProperty $property, string $className): string
    {
        // 当前类命名空间
        $selfNamespaceName = $property->getDeclaringClass()->getNamespaceName();
        // 直接匹配类
        if (class_exists($className)) {
            return $className;
        }
        // 判断是否是同一命名空间下的类
        elseif (class_exists($selfNamespaceName . '\\' . $className)) {
            return $selfNamespaceName . '\\' . $className;
        }
        // 判断是否是引用类
        else {
            // 获取引入文件
            $importClass = $this->parseUseStatements($property->getDeclaringClass());
            if (isset($importClass[$className])) {
                return $importClass[$className];
            }
        }

        throw new Exception('not find class ' . $className);
    }

    /**
     * 获取引入文件
     */
    private function parseUseStatements(ReflectionClass $reflectionClass): array
    {

        $content = file_get_contents($reflectionClass->getFileName());
        preg_match_all('/^\s*use[\s+](.*);$/m', $content, $matches);
        $classNames = [];
        foreach ($matches[1] as $fullClassName) {
            $parts                   = explode('\\', $fullClassName);
            $classNames[end($parts)] = $fullClassName;
        }

        return $classNames;
    }

    /**
     * Undocumented function
     *
     * @return TreeNode
     */
    public function getTree()
    {
        return $this->tree;
    }
}
