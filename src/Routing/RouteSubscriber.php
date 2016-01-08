<?php
/**
 * @file
 * Contains \Drupal\datatank\Routing\RouteSubscriber.
 */

namespace Drupal\datatank\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * App countroller.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('node.add')) {
      $route->setDefault('_title_callback', '\Drupal\datatank\Controller\AppController::addPageTitle');
    }
  }

}
