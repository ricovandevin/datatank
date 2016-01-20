<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Block\DatatankLanguageBlock.
 */

namespace Drupal\datatank\Plugin\Block;

use \Drupal\language\Plugin\Block\LanguageBlock;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'Language switcher' block.
 *
 * @Block(
 *   id = "datatank_language_block",
 *   admin_label = @Translation("DATATANK Language switcher"),
 *   category = @Translation("Datatank"),
 * )
 */
class DatatankLanguageBlock extends LanguageBlock implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    $entity = \Drupal::request()->attributes->get('node');
    if ($entity) {
      if (!$entity->hasTranslation('nl')) {
        unset($build['#links']['nl']);
      }

      if (!$entity->hasTranslation('en')) {
        unset($build['#links']['en']);
      }


    }

    return $build;
  }

}
