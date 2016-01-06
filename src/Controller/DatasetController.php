<?php

/**
 * @file
 * Contains \Drupal\datatank\Controller\DatasetController.
 */

namespace Drupal\datatank\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Controller\EntityViewController;

/**
 * Defines a controller to render a single dataset
 */
class DatasetController extends EntityViewController {

  /**
   * The _title_callback for the page that renders a single dataset.
   *
   * @param \Drupal\Core\Entity\EntityInterface $dataset
   *   The current dataset.
   *
   * @return string
   *   The page title.
   */
  public function title(EntityInterface $datatank_dataset) {
    return $datatank_dataset->field_dataset_title->value;

  }

}
