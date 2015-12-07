<?php

/**
 * @file
 * Contains \Drupal\node\DatasetAccessControlHandler.
 */

namespace Drupal\datatank;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the access control handler for the Datatank Dataset Entity.
 *
 * @see \datatank\entity\Dataset
 * @ingroup node_access
 */
class DatasetAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  /*public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // TODO
  }*/
}