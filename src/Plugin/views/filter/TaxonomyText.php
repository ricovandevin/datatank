<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\views\filter\TaxonomyText.
 */

namespace Drupal\datatank\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\StringFilter;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;


/**
 * Filter by Taxonomy in a text field
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("dataset_taxonomy_text")
 */
class TaxonomyText extends StringFilter {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueTitle = t('Category');
  }


  public function query() {
    $this->ensureMyTable();
    $storage = \Drupal::entityManager()->getStorage('datatank_dataset');
    $storage->getEntityType();

    // Field reference
    $configuration = [
      'table' => 'datatank_dataset__' . $this->realField,
      'field' => 'entity_id',
      'left_table' => 'datatank_dataset_field_data',
      'left_field' => 'did',
      'operator' => '='
    ];
    $join = Views::pluginManager('join')
      ->createInstance('standard', $configuration);
    $this->query->addRelationship($this->realField, $join, 'node_field_data');

    // Term data reference
    $configuration = [
      'table' => 'taxonomy_term_field_data',
      'field' => 'tid',
      'left_table' => $this->realField,
      'left_field' => $this->realField . '_target_id',
      'operator' => '='
    ];
    dsm($configuration);
    $join = Views::pluginManager('join')
      ->createInstance('standard', $configuration);
    $this->query->addRelationship('term_data', $join, $this->realField . '_target_id');

    $this->opContains('term_data.name');
  }
}
