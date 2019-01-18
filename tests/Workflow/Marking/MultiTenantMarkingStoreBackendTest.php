<?php

namespace App\Tests\Workflow\Marking;

use App\Workflow\Marking\MultiTenantMarkingStoreBackend;
use App\Workflow\Marking\MarkingStoreCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Validator\Validator;
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


}
