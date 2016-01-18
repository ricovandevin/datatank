<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\CKEditorPlugin\Iframe.
 */

namespace Drupal\datatank\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Iframe" plugin.
 *
 * @CKEditorPlugin(
 *   id = "iframe",
 *   label = @Translation("iframe"),
 * )
 */
class Iframe extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'datatank') . '/js/plugins/iframe/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array(
      'iframe_dialogTitleAdd' => t('Add Link'),
      'iframe_dialogTitleEdit' => t('Edit Link'),
    );
  }

  /**
   * {@inheritdoc}
   */
  function getDependencies(Editor $editor) {
    return array('fakeobjects');
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = drupal_get_path('module', 'datatank') . '/js/plugins/iframe/icons';
    return array(
      'Iframe' => array(
        'label' => t('Iframe'),
        'image' => $path . '/iframe.png',
      ),
    );
  }

}
