<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Field\FieldFormatter\TaxonomySearch.
 */

namespace Drupal\datatank\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'licence' formatter.
 *
 * @FieldFormatter(
 *   id = "taxonomy_search",
 *   label = @Translation("Taxonomy link to datatset search"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TaxonomySearch extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as $delta => &$element) {
      $url = Url::fromRoute('view.search.page_1', ['query' => ['f[0]' => 'type:' . $element['#title']], 'absolute' => TRUE]);
      $element['#url'] = $url;
      unset($element['#options']['language']);
    }

    return $elements;
  }

}
