<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedCollectionInterface;
use Doctrine\Common\Collections\ArrayCollection;

class MarkingCollection implements ComposedCollectionInterface {
    use \App\Traits\ComposedCollectionTrait;

    public function __construct() {
        $this->semanticParameter = 'Marking';
        $this->namePropery = 'MarkingId';
    }

    /**
     * Check if marking exists.
     *
     * @param Marking $marking
     * @return bool
     */
    public function hasMarking(Marking $marking) {
        return $this->hasChild($marking);
    }

    /**
     * Check if marking exists.
     *
     * @param string $markingID
     * @return bool
     */
    public function hasMarkingKey(string $markingID) {
        return $this->hasChildKey($markingID);
    }

    /**
     * Remove a marking
     *
     * @param Marking $marking
     * @return self
     */
    public protected function removeMarking(Marking $marking) {
        $this->removeChild($marking);
        return $this;
    }

    /**
     * Remove a marking
     *
     * @param string $markingId
     * @return null|Marking Removed marking
     */
    public function removeMarkingKey(string $markingId) {
        return $this->removeChildKey($markingId);
    }

    /*
     * Get a marking
     *
     * @param string $markingId
     * @return null|Marking
     */
    public function getMarking(string $markingId) {
        return $this->getChild($markingId);
    }

    /**
    * Set a MarkingId => Marking
    *
    * @param string $markingId
    * @param Marking $marking
    * @return self
    */
    public function setMarking(string $markingId, Marking $marking) {
        // todo validate $markingId matches $marking->getMarkingId()
        $this->addMarking($marking);
        return $this;
    }

    /**
     * Add a marking
     *
     * @param Marking $marking
     * @return self
     */
    public function addMarking(Marking $marking) {
        $markingId = $marking->getMarkingId();
        // todo exception markingIdMissing
        $this->setChild($markingId, $marking);
        return $this;
    }
}
