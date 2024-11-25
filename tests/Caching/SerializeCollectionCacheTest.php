<?php

namespace Astral\Serialize\Tests\Caching;

use Astral\Serialize\Support\Caching\SerializeCollectionCache;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SerializeCollectionCacheTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testHasReturnsTrueForExistingCache()
    {
        $className  = 'TestClass';
        $collection = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className, $collection);

        $this->assertTrue(SerializeCollectionCache::has($className));
    }

    /**
     * @throws Exception
     */
    public function testOverwriteExistingCacheEntryWhenPuttingNewCollection()
    {
        $className         = 'TestClass';
        $initialCollection = $this->createMock(DataGroupCollection::class);
        $newCollection     = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className, $initialCollection);
        $this->assertSame($initialCollection, SerializeCollectionCache::get($className));

        SerializeCollectionCache::put($className, $newCollection);
        $this->assertSame($newCollection, SerializeCollectionCache::get($className));
        $this->assertNotSame($initialCollection, SerializeCollectionCache::get($className));
    }

    /**
     * @throws Exception
     */
    public function testGetReturnsCorrectDataGroupCollectionForExistingCache()
    {
        $className  = 'TestClass';
        $collection = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className, $collection);

        $result = SerializeCollectionCache::get($className);

        $this->assertSame($collection, $result);
    }

    /**
     * @throws Exception
     */
    public function testSuccessfullyAddNewDataGroupCollectionToCache()
    {
        $className  = 'TestClass';
        $collection = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className, $collection);

        $this->assertTrue(SerializeCollectionCache::has($className));
        $this->assertSame($collection, SerializeCollectionCache::get($className));
    }

    public function testGetReturnsNullForNonExistentClass()
    {
        $className = 'NonExistentClass';

        $result = SerializeCollectionCache::get($className);

        $this->assertNull($result);
    }

    public function testHasReturnsFalseForNonExistingCache()
    {
        $className = 'NonExistentClass';

        $this->assertFalse(SerializeCollectionCache::has($className));
    }

    /**
     * @throws Exception
     */
    public function testMaintainSeparateCachesForDifferentClassNames()
    {
        $className1  = 'TestClass1';
        $className2  = 'TestClass2';
        $collection1 = $this->createMock(DataGroupCollection::class);
        $collection2 = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className1, $collection1);
        SerializeCollectionCache::put($className2, $collection2);

        $this->assertTrue(SerializeCollectionCache::has($className1));
        $this->assertTrue(SerializeCollectionCache::has($className2));
        $this->assertSame($collection1, SerializeCollectionCache::get($className1));
        $this->assertSame($collection2, SerializeCollectionCache::get($className2));
        $this->assertNotSame(SerializeCollectionCache::get($className1), SerializeCollectionCache::get($className2));
    }

    /**
     * @throws Exception
     */
    public function testHandleCaseSensitivityCorrectlyInClassNames()
    {
        $upperCaseClassName = 'TestClass';
        $lowerCaseClassName = 'testCases';
        $collectionUpper    = $this->createMock(DataGroupCollection::class);
        $collectionLower    = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($upperCaseClassName, $collectionUpper);
        SerializeCollectionCache::put($lowerCaseClassName, $collectionLower);

        $this->assertTrue(SerializeCollectionCache::has($upperCaseClassName));
        $this->assertTrue(SerializeCollectionCache::has($lowerCaseClassName));
        $this->assertNotSame(SerializeCollectionCache::get($upperCaseClassName), SerializeCollectionCache::get($lowerCaseClassName));
        $this->assertSame($collectionUpper, SerializeCollectionCache::get($upperCaseClassName));
        $this->assertSame($collectionLower, SerializeCollectionCache::get($lowerCaseClassName));
    }

    /**
     * @throws Exception
     */
    public function testReturnSameReferenceWhenGettingCacheMultipleTimes()
    {
        $className  = 'TestClass';
        $collection = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className, $collection);

        $firstGet  = SerializeCollectionCache::get($className);
        $secondGet = SerializeCollectionCache::get($className);

        $this->assertSame($firstGet, $secondGet);

        // Modify the first reference
        //        $firstGet->someMethod();

        $thirdGet = SerializeCollectionCache::get($className);

        // Ensure the third get still returns the same (now modified) reference
        $this->assertSame($firstGet, $thirdGet);
    }

    public function testNotAffectOtherCachesWhenModifyingOrRemovingOneEntry()
    {
        $className1  = 'TestClass1';
        $className2  = 'TestClass2';
        $collection1 = $this->createMock(DataGroupCollection::class);
        $collection2 = $this->createMock(DataGroupCollection::class);

        SerializeCollectionCache::put($className1, $collection1);
        SerializeCollectionCache::put($className2, $collection2);

        // Modify one cache entry
        $modifiedCollection = $this->createMock(DataGroupCollection::class);
        SerializeCollectionCache::put($className1, $modifiedCollection);

        // Check if the other cache entry remains unaffected
        $this->assertSame($collection2, SerializeCollectionCache::get($className2));

        // Remove one cache entry
        SerializeCollectionCache::put($className1, null);

        // Check if the other cache entry still exists and remains unaffected
        $this->assertTrue(SerializeCollectionCache::has($className2));
        $this->assertSame($collection2, SerializeCollectionCache::get($className2));
    }
}
