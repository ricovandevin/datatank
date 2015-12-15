<?php

/**
 * @file
 * Contains \Drupal\datatank\Entity\DatasetViewsData.
 */

namespace Drupal\datatank\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the user entity type.
 */
class DatasetViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['datatank_dataset']['table']['did'] = [
      'field' => 'did',
      'title' => t('Dataset Id'),
      'help' => t('Dataset did')
    ];

    $filters = datatank_helper_get_tax_fields();
    unset($filters['field_dataset_keyword']);

    foreach ($filters as $field_name => $id) {
      $data['datatank_dataset'][$field_name] = array(
        'title' => t('Filter by dataset @name', array('@name' => $id)),
        'help' => t('Filters out @name.', array('@name' => $id)),
        'filter' => array(
          'field' => $field_name,
          'id' => 'dataset_taxonomy',
          'label' => $id,
        ),
      );
    }

    $data['datatank_dataset']['field_dataset_keyword'] = array(
      'title' => t('Filter keyword @name', array('@name' => 'field_dataset_keyword')),
      'help' => t('Filters out @name.', array('@name' => 'field_dataset_keyword')),
      'filter' => array(
        'field' => 'field_dataset_keyword',
        'id' => 'dataset_taxonomy_text',
        'label' => 'field_dataset_keyword',
      ),
    );

    return $data;
  }

}
