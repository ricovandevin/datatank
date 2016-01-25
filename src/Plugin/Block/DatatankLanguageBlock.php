<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Block\DatatankLanguageBlock.
 */

namespace Drupal\datatank\Plugin\Block;

use \Drupal\language\Plugin\Block\LanguageBlock;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Language\LanguageManagerInterface;

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
   * Constructs an LanguageBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LanguageManagerInterface $language_manager, PathMatcherInterface $path_matcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $language_manager, $path_matcher);
  }

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

  /**
   * {@inheritdoc}
   *
   * @todo Make cacheable in https://www.drupal.org/node/2232375.
   */
  public function getCacheMaxAge() {
    return 0;
  }


}
