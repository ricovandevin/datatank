<?php

/**
 * @file
 * Contains \Drupal\datatank\Entity\Controller\ColumnListBuilder.
 */

namespace Drupal\datatank\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for datatank_dataset entity.
 *
 * @ingroup datatank
 */
class DatasetListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['column_name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\datatank\Entity\Column */
    $row['id'] = $entity->id();
    $row['column_name'] = $entity->link();
    return $row + parent::buildRow($entity);
  }

}

?>