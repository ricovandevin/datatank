<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Block\DatatankShare.
 */

namespace Drupal\datatank\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'DatatankShare' block.
 *
 * @Block(
 *  id = "datatank_share",
 *  admin_label = @Translation("Datatank share"),
 * )
 */
class DatatankShare extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get url
    $current_url = Url::fromRoute('<current>');
    $current_url->setAbsolute(TRUE);

    // Get title
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $title = \Drupal::service('title_resolver')
      ->getTitle($request, $route_match->getRouteObject());
    $title = urlencode($title);

    // Twitter url
    $twitter_url = Url::fromUri("http://twitter.com/share?url=" . $current_url->toString() . "&text=" . $title);
    $twitter_url->setOption('attributes', array('target' => '_blank'));

    // Facebook url
    $facebook_url = Url::fromUri("http://www.facebook.com/sharer.php??u=" . $current_url->toString() . "&t=" . $title);
    $facebook_url->setOption('attributes', array('target' => '_blank'));

    $build = [];
    $build['twitter']['#markup'] = \Drupal::l(t('Share on twitter'), $twitter_url);
    $build['facebook']['#markup'] = \Drupal::l(t('Share on facebook'), $facebook_url);

    $build['#cache'] = array(
      //'contexts' => array('url'),
      'max-age' => 0,
    );
    return $build;
  }

}
