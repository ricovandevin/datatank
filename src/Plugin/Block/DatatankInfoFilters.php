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
    $config = \Drupal::configFactory()->getEditable('datatank.settings');
    $url = Url::fromUserInput($config->get('datatank_link_info_filters'));
    $info_link = \Drupal::l('?', $url);;

    $build = [];
    $build['datatank_info_filters']['#markup'] = t('Filter datasets') . '<span class="info_link">' . $info_link . '</span>';

    return $build;
  }

}
