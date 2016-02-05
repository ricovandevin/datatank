<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\CKEditorPlugin\FakeObjects.
 */

namespace Drupal\datatank\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "FakeObjects" plugin.
 *
 * @CKEditorPlugin(
 *   id = "fakeobjects",
 *   label = @Translation("FakeObjects"),
 * )
 */
class FakeObjects extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'datatank') . '/js/plugins/fakeobjects/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return array();
  }

}
