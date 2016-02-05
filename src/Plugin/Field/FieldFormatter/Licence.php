<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Field\FieldFormatter\Licence.
 */

namespace Drupal\datatank\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'licence' formatter.
 *
 * @FieldFormatter(
 *   id = "licence",
 *   label = @Translation("Taxonomy licence link to defined url"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class Licence extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($elements as $delta => &$element) {
      $url = Url::fromUserInput('/node/16');
      $element['#url'] = $url;
      unset($element['#options']['language']);
    }

    return $elements;
  }

}
