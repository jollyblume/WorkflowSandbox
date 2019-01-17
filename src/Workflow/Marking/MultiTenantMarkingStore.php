<?php

namespace App\Workflow\Marking;

/*
 * Forked from symfony/workflow
 */

use Symfony\Component\Workflow\Marking as BaseMarking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use App\Workflow\MultiTenantMarkingStoreBackendInterface;
use App\Exception\PropImmutableException;
use App\Exception\OutOfScopeException;
use App\Exception\PropRequiredException;
use App\Traits\PropertyAccessorTrait;

/**
 * MultiTenantMarkingStore
 *
 * MultiTenantMarkingStore is forked from the symfony/workflow component and implements
 * the component's MarkingStoreInterface.
 *
 * This marking store maintains the marking for every subject participating in
 * this workflow. Marking stores are persisted to a MultiTenantMarkingStoreBackend.
 *
 * Each MarkingStore instance has a UUID, called 'markingStoreId'. This is used
 * to uniquely identify the marking store and it's related workflow.
 *
 * Each subject (token) participating in any workflow will be injected with a
 * UUID, called 'markingId'. This will only be injected once and will uniquely
 * identify this subject (token) throughout the marking store backend.
 */
class MultiTenantMarkingStore implements MarkingStoreInterface {
    use PropertyAccessorTrait;

    const MARKING_ID_PROPERTY = 'markingId';
    const MARKING_STORE_NAME = 'workflow.marking-store';
    const MARKING_NAME = 'workflow.marking';

    /**
     * Marking Store ID
     *
     * @var string $markingStoreId
     */
    private $markingStoreId;

    /**
     * Marking store backend
     *
     * @var MultiTenantMarkingStoreBackendInterface $backend
     */
    private $backend;

    public function __construct(MultiTenantMarkingStoreBackendInterface $backend) {
        $this->backend = $backend;
        $this->markingStoreId = $backend->createId(self::MARKING_STORE_NAME);
    }

    /**
    * Get this marking store id
    *
    * @return string markingStoreId
    */
    public function getMarkingStoreId() :string {
        return $this->markingStoreId;
    }

    /**
     * Get the marking store persistance backend
     *
     * @return MultiTenantMarkingStoreBackendInterface
     */
    public function getMarkingStoreBackend() {
        return $this->backend;
    }

    /**
     * Assert a subject is valid
     *
     * @throws OutOfScopeException
     */
    protected function assertValidSubject($subject) {
        $isReadable = $this->isPropertyValueReadable($subject, self::MARKING_ID_PROPERTY);
        if (!$isReadable) {
            $contextParameters = [
                'object' => $subject,
                'property' => self::MARKING_ID_PROPERTY,
                'debug' => 'subject must support MarkableSubjectInterface',
            ];
            throw new OutOfScopeException($contextParameters);
        }
    }

    /**
     * Get the markingId from a subject (token)
     *
     * Side effect:
     *  If the subject doesn't have a markingId, one will be created and applied.
     *
     * @param mixed $subject Must support MarkableSubjectInterface
     * @return string markingId
     * @throws OutOfScopeException Must support MarkableSubjectInterface
     *                             MarkableSubjectInterface does not have to be
     *                             implemented by the subject. The methods from
     *                             the interface must be implemented.
     */
    public function getMarkingId($subject) {
        $this->assertValidSubject($subject);
        $markingId = $this->getPropertyValue($subject, self::MARKING_ID_PROPERTY);
        if (empty($markingId)) {
            $markingId = $this->getMarkingStoreBackend()->createId(self::MARKING_NAME);
            $this->setPropertyValue(
                $subject,
                self::MARKING_ID_PROPERTY,
                $markingId
            );
        }
        return $markingId;
    }

    /**
    * Assert the markingId from both $subject and $marking match
    *
    * @throws \Exception
    */
    protected function assertIdMatchesMarking($subject, Marking $marking) {
        $markingId = $this->getMarkingId($subject);
        $expectedMarkingId = $marking->getMarkingId();
        if ($markingId !== $expectedMarkingId) {
            throw new \Exception();
        }
    }

    /**
     * Get a subject's workflow marking for this marking store
     *
     * @param string $subject The subject or token
     * @return BaseMarking The subject's marking within this marking store.
     *                     BaseMarking is the default when no marking exists for
     *                      the subject within this store.
     *                     Marking (inherited from BaseMarking) is returned when
     *                      results come from the backend.
     */
    public function getMarking($subject) {
        $markingId = $this->getMarkingId($subject);
        $markingStoreId = $this->getMarkingStoreId();
        return $this->getMarkingStoreBackend()->getMarking($markingStoreId, $markingId);
    }

    /**
     * Set the subject's workflow marking for this marking store
     *
     * @param string $subject The subject or token
     * @param BaseMarking The subject's marking within this marking store.
     *                    The BaseMarking is converted to a Marking internally.
     * @return self
     */
    public function setMarking($subject, BaseMarking $marking) {
        $this->assertValidSubject($subject);
        if (!$marking instanceof Marking) {
            $markingId = $this->getMarkingId($subject) ;
            $places = $marking->getPlaces();
            $marking = new Marking($subject, $places);
        }
        $this->assertIdMatchesMarking($subject, $marking);

        $markingStoreId = $this->getMarkingStoreId();
        $this->getMarkingStoreBackend()->setMarking($markingStoreId, $marking);
        return $this;
    }
}
