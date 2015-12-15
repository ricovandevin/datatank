<?php
/**
 * @file
 * Contains \Drupal\datatank\Controller\DatasetSearchBuilder.
 */

namespace Drupal\datatank\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity;

class DatasetSearchBuilder extends ControllerBase {

  public function title() {
    $datasets = entity_load_multiple('datatank_dataset');

    return t('Open datasets (@number)', ['@number' => count($datasets)]);
  }

  public function content() {
    $query = \Drupal::entityQuery('datatank_dataset');

    $nids = $query->execute();
    if (!empty($nids)) {
      $datasets = entity_load_multiple('datatank_dataset', $nids);
      $build = entity_view_multiple($datasets, 'teaser');
      return $build;
    }
  }


}

?>