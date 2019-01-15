<?php

namespace App\Collection;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * ComposedCollectionInterface.
 *
 * ComposedCollectionInterface extends the doctrine/collections interfaces
 * implemented by ArrayCollection.
 */
interface ComposedArrayCollectionInterface extends Collection, Selectable
{
}
