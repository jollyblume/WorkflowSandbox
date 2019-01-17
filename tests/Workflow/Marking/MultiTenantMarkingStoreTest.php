<?php

namespace App\Tests\Workflow\Marking;

use App\Workflow\Marking\MultiTenantMarkingStore;
use App\Workflow\Marking\MultiTenantMarkingStoreBackendInterface;
use PHPUnit\Framework\TestCase;

class MultiTenantMarkingStoreTest extends TestCase {
    protected function buildMarkingStore($backend = null) {
        if (!$backend) {
            $backend = $this
                ->getMockBuilder(MultiTenantMarkingStoreBackendInterface::class)
                ->getMock();
        }
        $backend
            ->method('createId')->will($this->returnValue('test.id'));
        $markingStore = $this->getMockBuilder(MultiTenantMarkingStore::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $markingStore
            ->method('getMarkingStoreId')->will($this->returnValue('test.marking-store-id'));
        $markingStore
            ->method('getMarkingStoreBackend')->will($this->returnValue($backend));
        return $markingStore;
    }

    protected function buildSubject(string $id = '') {
        $subject = new class($id) {
            private $markingId;
            public function __construct(string $markingId = '') {
                $this->markingId = $markingId;
            }
            public function getMarkingId() {
                return $this->markingId;
            }
            public function setMarkingId(string $id) {
                $this->markingId = $id;
                return $this;
            }
        };
        return $subject;
    }

    protected function buildInvalidSubject() {
        return new class(){};
    }

    // todo does this test anything since I'm using a mock?
    public function testGetMarkingStoreId() {
        $markingStore = $this->buildMarkingStore();
        $this->assertEquals('test.marking-store-id', $markingStore->getMarkingStoreId());
    }

    public function testGetMarkingStoreBackend() {
        $backend = $this
            ->getMockBuilder(MultiTenantMarkingStoreBackendInterface::class)
            ->getMock();
        $store = $this->buildMarkingStore($backend);
        $this->assertEquals($backend, $store->getMarkingStoreBackend());
    }

    public function testGetMarkingIdGetsDefaultIfNotSet() {
        $this->markTestSkipped('Not working with the mocks being used');
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject(); // subject with no markingId
        $markingId = $store->getMarkingId($subject);
        $this->assertEquals('test.id', $markingId);
    }

    public function testGetMarkingIdSetsDefaultOnSubjectIfMissing() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject(); // subject with no markingId
        $markingId = $store->getMarkingId($subject);
        $this->assertEquals($markingId, $subject->getMarkingId());
    }

    public function testGetMarkingIdWithExistingMarking() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject('test.id.new');
        $this->assertEquals('test.id.new', $subject->getMarkingId());
    }

    /** @expectedException \Exception */
    public function testGetMarkingIdAssertValidSubject() {
        $this->markTestSkipped('?Not working with the mocks being used');
        $store = $this->buildMarkingStore();
        $subject = $this->buildInvalidSubject();
        $store->getMarking($subject);
        $this->assertTrue(false);
    }

    // public function testSymfonyWorkflowCompatibilityGetSetMarking()
    // {
    //     $subject = $this->buildSubject();
    //     $subject->myMarks = null;
    //
    //     $markingStore = new MultipleStateMarkingStore('myMarks');
    //
    //     $marking = $markingStore->getMarking($subject);
    //
    //     $this->assertInstanceOf(Marking::class, $marking);
    //     $this->assertCount(0, $marking->getPlaces());
    //
    //     $marking->mark('first_place');
    //
    //     $markingStore->setMarking($subject, $marking);
    //
    //     $this->assertSame(array('first_place' => 1), $subject->myMarks);
    //
    //     $marking2 = $markingStore->getMarking($subject);
    //
    //     $this->assertEquals($marking, $marking2);
    // }
}
