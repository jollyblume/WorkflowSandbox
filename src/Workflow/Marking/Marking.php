<?php

namespace App\Workflow\Marking;

use Symfony\Component\Workflow\Marking as BaseMarking;

class Marking extends BaseMarking {
    /**
     * Marking UUID
     *
     * @var string $markingId
     */
    private $markingId;

    public function __construct(string $markingId, array $places = []) {
        $this->markingId = $markingId;
        parent::__construct($places);
    }

    public function getMarkingId() {
        return $this->markingId;
    }
}
