<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedCollectionInterface;
use Doctrine\Common\Collections\ArrayCollection;

class MarkingStoreCollection implements ComposedCollectionInterface {
    use \App\Traits\ComposedCollectionTrait;

     */
    public function __construct() {
        $this->semanticParameter = 'MarkingStore';
        $this->namePropery = 'MarkingStoreId';
    }

    /**
     * Check if marking store exists.
     *
     * @param MarkingCollection $markingStore
     * @return bool
     */
    public function hasMarkingStore(MarkingCollection $markingStore) {
        return $this->hasChild($markingStore);
    }

    /**
     * Check if marking store exists.
     *
     * @param string $markingStoreID
     * @return bool
     */
    public function hasMarkingStoreKey(string $markingStoreID) {
        return $this->hasChildKey($markingStoreID);
    }

    /**
     * Remove a marking store
     *
     * @param MarkingCollection $markingStore
     * @return null|MarkingCollection Removed marking store
     */
    public protected function removeMarkingStore(MarkingCollection $markingStore) {
        return $this->removeChild($markingStore);
    }

    /**
     * Remove a marking store
     *
     * @param string $markingStoreId
     * @return null|MarkingCollection Removed marking store
     */
    public function removeMarkingStoreKey(string $markingStoreId) {
        return $this->removeChildKey($markingStoreId);
    }

    /*
     * Get a marking store
     *
     * @param string $markingStoreId
     * @return null|MarkingCollection
     */
    public function getMarkingStore(string $markingStoreId) {
        return $this->getChild($markingStoreId);
    }

    /**
    * Set a MarkingStoreId => MarkingCollection
    *
    * @param string $markingStoreId
    * @param MarkingCollection $markingStore
    * @return self
    */
    public function setMarkingStore(string $markingStoreId, MarkingCollection $markingStore) {
        // todo validate $markingStoreId matches $markingStore->getMarkingStoreId()
        $this->addMarkingStore($markingStore);
        return $this;
    }

    /**
     * Add a marking store
     *
     * @param MarkingCollection $markingStore
     * @return self
     */
    public function addMarkingStore(MarkingCollection $markingStore) {
        $markingStoreId = $markingStore->getMarkingStoreId();
        // todo exception markingStoreIdMissing
        $this->setChild($markingStoreId, $markingStore);
        return $this;
    }
}
