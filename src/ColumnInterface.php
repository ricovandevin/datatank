<?php

/**
 * @file
 * Contains \Drupal\datatank\ColumnInterface.
 */

namespace Drupal\datatank;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Column entity.
 * @ingroup datatank
 */
interface ColumnInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

?>