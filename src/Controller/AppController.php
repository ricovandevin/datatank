<?php

/**
 * @file
 * Contains \Drupal\datatank\Controller\AppController.
 */

namespace Drupal\datatank\Controller;

use Drupal\node\NodeTypeInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Defines a controller for the app content type
 */
class AppController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The _title_callback for the node.add route.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   The current node.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(NodeTypeInterface $node_type) {
    if ($node_type->label() == 'App') {
      return t('Submit your app');
    }
  }

}
