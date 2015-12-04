<?php

/**
 * @file
 * Contains \Drupal\datatank\DatasetInterface.
 */

namespace Drupal\datatank;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Dataset entity.
 * @ingroup datatank
 */
interface DatasetInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

?>