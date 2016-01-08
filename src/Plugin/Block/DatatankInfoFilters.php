<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Block\DatatankInfoFilters.
 */

namespace Drupal\datatank\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'DatatankInfoFilters' block.
 *
 * @Block(
 *  id = "datatank_info_filters",
 *  admin_label = @Translation("Datatank info filters"),
 * )
 */
class DatatankInfoFilters extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $url = Url::fromUserInput('/node/1');
    $info_link = \Drupal::l(t('?'), $url);

    $build = [];
    $build['datatank_info_filters']['#markup'] = t('Filter datasets') . '<span class="info_link">' . $info_link . '</span>';

    return $build;
  }

}
