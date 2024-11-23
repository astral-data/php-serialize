<?php

use Astral\Serialize\Context;
use Astral\Serialize\Support\Caching\GlobalDataCollectionCache;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;

beforeEach(function () {
    /** @var Context */
    $this->context = new Context(TypeOneDoc::class, []);
});

it('test parse serialize class', function () {
    $result =  $this->context->parseSerializeClass(Context::DEFAULT_GROUP_NAME, TypeOneDoc::class);
    print_r(GlobalDataCollectionCache::toArray());
});
