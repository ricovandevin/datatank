<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Block\TargetGroupBlock.
 */

namespace Drupal\datatank\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Example: uppercase this please' block.
 *
 * @Block(
 *   id = "datatank_filter_target_group",
 *   admin_label = @Translation("Datatank Target Group filter")
 *
 * )
 */
class TargetGroupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $values = datatank_helper_get_entries_of_field('field_dataset_target_group');
    return array(
      '#markup' => $this->t("Deze michiel"),
      '#cache' => array(
        'max-age' => 0,
      ),
    );
  }

}
