<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\views\filter\Taxonomy.
 */

namespace Drupal\datatank\Plugin\views\filter;

use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\ManyToOne;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;


/**
 * Filter by Taxonomy
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("dataset_taxonomy")
 */
class Taxonomy extends ManyToOne {

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueTitle = t('Category');
    $this->definition['options callback'] = array($this, 'generateOptions');

  }


  public function query() {
    $this->ensureMyTable();
    $storage = \Drupal::entityManager()->getStorage('datatank_dataset');
    $storage->getEntityType();

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

    $values = array_values($this->value);
    $values = array_unique($values);
    if ($values[0] || count($values) > 1) {
      $db_or = db_or();
      $db_or->condition($this->realField . '_target_id', $this->value, 'IN');
      $this->query->addWhere($this->options['group'], $db_or);
    }

  }

  /**
   * Helper function that generates the options.
   * @return array
   */
  public function generateOptions() {
    $storage = \Drupal::entityManager()->getStorage('taxonomy_term');

    $relatedContentQuery = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', $this->realField);
    $relatedContentIds = $relatedContentQuery->execute(); //returns an array of node ID's

    $res = array();
    foreach ($relatedContentIds as $contentId) {
      $res[$contentId] = $storage->load($contentId)->getName();
    }
    return $res;
  }

  public function getNumber() {

  }

}
