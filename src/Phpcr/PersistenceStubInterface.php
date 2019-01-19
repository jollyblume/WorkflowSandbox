<?php

namespace App\Phpcr;

interface PersistenceStubInterface {
    public function getTransitionStatus();
    public function setTransitionStatus(string $status);
}
