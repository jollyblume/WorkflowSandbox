<?php

namespace App\Tests\Workflow\Marking;

use App\Workflow\Marking\MultiTenantMarkingStoreBackend;
use App\Workflow\Marking\Marking;
use App\Workflow\Marking\MarkingStoreCollection;
use Ramsey\Uuid\Uuid;
use App\Validator\Validator;
use PHPUnit\Framework\TestCase;

class MultiTenantMarkingStoreBackendTest extends TestCase {
    protected function buildBackend(?MarkingStoreCollection $markingStoreCollection = null) {
        $backend = new MultiTenantMarkingStoreBackend($markingStoreCollection);
        return $backend;
    }

    public function testCreateId() {
        $backend = $this->buildBackend();
        $id = $backend->createId('workflow.test');
        $validator = new Validator();
        $this->assertTrue($validator->validate($id));
    }

    public function testSetMarkingOkWhenStoreNotCreated() {
        $backend = $this->buildBackend();
        $markingStoreId = 'workflow.test-marking-store';
        $markingId = 'workflow.test-marking';
        $marking = new Marking($markingId);
        $backend->setMarking($markingStoreId, $marking);
        $recoveredMarking = $backend->getMarking($markingStoreId, $markingId);
        $this->assertEquals($marking, $recoveredMarking);
    }

    public function testSetMarkingOkWhenStoreCreated() {
        $backend = $this->buildBackend();
        $markingStoreId = 'workflow.test-marking-store';
        $markingId = 'workflow.test-marking';
        $marking1 = new Marking($markingId);
        $backend->setMarking($markingStoreId, $marking1);
        $marking2 = new Marking($markingId, ['place1']);
        $backend->setMarking($markingStoreId, $marking2);
        $recoveredMarking = $backend->getMarking($markingStoreId, $markingId);
        $this->assertEquals($marking2, $recoveredMarking);
    }

    public function testSetMarkingOkWhenMultipleStoresCreated() {
        $backend = $this->buildBackend();

        $markingStore1 = 'test.marking-store-1';
        $markinga1 = new Marking('test.marking-a1');
        $markinga2 = new Marking('test.marking-a2');
        $backend->setMarking($markingStore1, $markinga1);
        $backend->setMarking($markingStore1, $markinga2);
        $founda1 = $backend->getMarking($markingStore1, 'test.marking-a1');
        $founda2 = $backend->getMarking($markingStore1, 'test.marking-a2');

        $markingStore2 = 'test.marking-store-2';
        $markingb1 = new Marking('test.marking-b1');
        $markingb2 = new Marking('test.marking-b2');
        $backend->setMarking($markingStore2, $markingb1);
        $backend->setMarking($markingStore2, $markingb2);
        $foundb1 = $backend->getMarking($markingStore2, 'test.marking-b1');
        $foundb2 = $backend->getMarking($markingStore2, 'test.marking-b2');

        $this->assertEquals($markinga1, $founda1);
        $this->assertEquals($markinga2, $founda2);
        $this->assertEquals($markingb1, $foundb1);
        $this->assertEquals($markingb2, $foundb2);

        return $backend;
    }

    /** @depends testSetMarkingOkWhenMultipleStoresCreated */
    public function testSetMarkingOkWhenMultipleMarkingsRecreated($backend) {
        $markingStore1 = 'test.marking-store-1';
        $markinga1 = new Marking('test.marking-a1', ['placea1']);
        $markinga2 = new Marking('test.marking-a2', ['placea2']);
        $backend->setMarking($markingStore1, $markinga1);
        $backend->setMarking($markingStore1, $markinga2);
        $founda1 = $backend->getMarking($markingStore1, 'test.marking-a1');
        $founda2 = $backend->getMarking($markingStore1, 'test.marking-a2');

        $markingStore2 = 'test.marking-store-2';
        $markingb1 = new Marking('test.marking-b1', ['placeb1']);
        $markingb2 = new Marking('test.marking-b2', ['placeb2']);
        $backend->setMarking($markingStore2, $markingb1);
        $backend->setMarking($markingStore2, $markingb2);
        $foundb1 = $backend->getMarking($markingStore2, 'test.marking-b1');
        $foundb2 = $backend->getMarking($markingStore2, 'test.marking-b2');

        $this->assertEquals($markinga1, $founda1);
        $this->assertEquals($markinga2, $founda2);
        $this->assertEquals($markingb1, $foundb1);
        $this->assertEquals($markingb2, $foundb2);

        return $backend;
    }
}
