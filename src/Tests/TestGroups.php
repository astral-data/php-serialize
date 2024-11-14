<?php

declare(strict_types=1);

namespace Asrtal\Serialize\Tests;


use Asrtal\Serialize\Annotations\Groups;
use Asrtal\Serialize\Annotations\PropertyAlisa;
use Asrtal\Serialize\Annotations\PropertyAlisaByGroup;

#[Groups(['test', 'test_2'])]
class TestGroups extends Serialize
{
    /**
     * @var string pid
     */
    #[Groups('test_2')]
    #[PropertyAlisa('pid-alisa')]
    public $pid;

    /**
     * @var string test
     */
    #[Groups('test')]
    #[PropertyAlisa('test-alisa')]
    #[PropertyAlisaByGroup('test-alisa-2', ['test'])]
    public $names;

    /**
     * @var testGroupData[] lists
     */
    #[Groups(['test', 'test_2'])]
    public $lists;
}

class testGroupData
{
    /** @var string name */
    public $abc;

    /** @var string id */
    #[Groups('test')]
    public $id;

    /** @var string name */
    #[Groups('test')]
    public $name;

    /**
     * @var testGroupDataGroups groups
     */
    #[Groups('test')]
    public $groups;
}

class testGroupDataGroups
{
    /** @var string name */
    #[Groups('test')]
    public $groupsTwo;

    /**
     * @var string name
     *
     * @ignore
     * */
    public $groups3;
}
