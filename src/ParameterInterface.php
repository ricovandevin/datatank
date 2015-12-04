<?php

/**
 * @file
 * Contains \Drupal\datatank\ParameterInterface.
 */

namespace Drupal\datatank;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Parameter entity.
 * @ingroup datatank
 */
interface ParameterInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

?>