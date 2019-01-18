<?php

namespace App\Tests\Workflow\Marking;

use App\Workflow\Marking\MultiTenantMarkingStore;
use App\Workflow\Marking\MultiTenantMarkingStoreBackend;
use App\Workflow\Marking\MarkingStoreCollection;
use App\Workflow\Marking\Marking;
use Symfony\Component\Workflow\Marking as BaseMarking;
use PHPUnit\Framework\TestCase;

// todo remove dependancy on external backend class
class MultiTenantMarkingStoreTest extends TestCase {
    protected function buildMarkingStoreCollection(string $markingStoreCollectionId = 'test.marking-store-id') {
        $markingStoreCollection = new MarkingStoreCollection($markingStoreCollectionId);
        return $markingStoreCollection;
    }

    protected function buildBackend(string $markingStoreCollectionId = 'test.marking-store-id') {
        $markingStoreCollection = $this->buildMarkingStoreCollection($markingStoreCollectionId);
        $backend = new MultiTenantMarkingStoreBackend($markingStoreCollection);
        return $backend;
    }

    protected function buildMarkingStore($backend = null, string $markingStoreId = '') {
        if (!$backend) {
            $backend = $this->buildBackend();
        }
        $markingStore = new MultiTenantMarkingStore($backend, $markingStoreId);
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

    public function testCompareMarkingsTrueFor2EmptyBaseMarkings() {
        $store = $this->buildMarkingStore();
        $marking1 = new BaseMarking();
        $marking2 = new BaseMarking();
        $this->assertTrue($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsTrueFor2BaseMarkingsWithEqualPlacesArray() {
        $store = $this->buildMarkingStore();
        $places1 = $store->convertPlacesForBaseMarking(['place1']);
        $marking1 = new BaseMarking($places1);
        $places2 = $store->convertPlacesForBaseMarking(['place1']);
        $marking2 = new BaseMarking($places2);
        $this->assertTrue($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsTrueFor2MarkingsWithEqualPlacesArray() {
        $store = $this->buildMarkingStore();
        $places1 = ['place1'];
        $marking1 = new Marking('test.marking', $places1);
        $places2 = ['place1'];
        $marking2 = new Marking('test.marking', $places2);
        $this->assertTrue($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsTrueFor1BaseMarkingAnd1MarkingsWithEqualPlacesArray() {
        $store = $this->buildMarkingStore();
        $places1 = $store->convertPlacesForBaseMarking(['place1']);
        $marking1 = new BaseMarking($places1);
        $places2 = ['place1'];
        $marking2 = new Marking('test.marking', $places2);
        $this->assertTrue($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsFalseFor2BaseMarkingsWithDifferentPlaces() {
        $store = $this->buildMarkingStore();
        $marking1 = new BaseMarking();
        $places2 = $store->convertPlacesForBaseMarking(['place1']);
        $marking2 = new BaseMarking($places2);
        $this->assertFalse($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsFalseFor1BaseMarkingAnd1MarkingsWithDifferentPlaces() {
        $store = $this->buildMarkingStore();
        $places1 = $store->convertPlacesForBaseMarking(['place1']);
        $marking1 = new BaseMarking($places1);
        $places2 = ['place2'];
        $marking2 = new Marking('test.marking', $places2);
        $this->assertFalse($store->compareMarkings($marking1, $marking2));
    }

    public function testCompareMarkingsFalseFor2MarkingsWithDifferentMarkingId() {
        $store = $this->buildMarkingStore();
        $marking1 = new Marking('test.marking-1');
        $marking2 = new Marking('test.marking-2');
        $this->assertFalse($store->compareMarkings($marking1, $marking2));
    }

    public function testGetMarkingStoreId() {
        $markingStore = $this->buildMarkingStore(null, 'test.marking-store-id');
        $this->assertEquals('test.marking-store-id', $markingStore->getMarkingStoreId());
    }

    public function testGetMarkingStoreBackend() {
        $backend = $this->buildBackend();
        $store = $this->buildMarkingStore($backend);
        $this->assertEquals($backend, $store->getMarkingStoreBackend());
    }

    public function testGetMarkingIdGetsDefaultIfNotSet() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject('test.id');
        $markingId = $store->getMarkingId($subject);
        $this->assertEquals('test.id', $markingId);
    }

    public function testGetMarkingIdSetsDefaultOnSubjectIfMissing() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject();
        $markingId = $store->getMarkingId($subject);
        $this->assertNotEmpty($markingId);
        $this->assertEquals($subject->getMarkingId(), $markingId);
    }

    public function testGetMarkingIdWithExistingMarking() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject('test.id.new');
        $markingId = $store->getMarkingId($subject);
        $this->assertEquals('test.id.new', $subject->getMarkingId());
    }

    /** @expectedException \Exception */
    public function testGetMarkingIdAssertValidSubject() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildInvalidSubject();
        $store->getMarking($subject);
        $this->assertTrue(false);
    }

    /** @expectedException \Exception */
    public function testAssertIdMatchesMarkingFailsWhenTheyMismatch() {
        $store = $this->buildMarkingStore();
        $subject = $this->buildSubject('test.1');
        $marking = new Marking('test.2');
        $store->setMarking($subject, $marking);
        $this->assertTrue(false);
    }

    /** forked from symfony/workflow tests */
    public function testGetSetMarking()
    {
        $subject = $this->buildSubject();
        $markingStore = $this->buildMarkingStore();

        $marking = $markingStore->getMarking($subject);
        $this->assertFalse(method_exists($marking, 'getMarkingId'));
        $this->assertCount(0, $marking->getPlaces());

        $marking->mark('first_place');
        $markingStore->setMarking($subject, $marking);

        $backendMarking = $markingStore->getMarking($subject);
        // todo define equality interface on marking
        $markingPlaces = $marking->getPlaces();
        $backendMarkingPlaces = $backendMarking->getPlaces();
        $this->assertEquals($markingPlaces, $backendMarkingPlaces);
        $this->assertTrue(method_exists($backendMarking, 'getMarkingId'));

        $this->assertTrue($markingStore->compareMarkings($marking, $backendMarking));
    }
}
