<?php
/**
 * @file
 * Contains \Drupal\datatank\Plugin\migrate\source
 */
namespace Drupal\datatank\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\tdt_client\Consumer;
use Drupal\tdt_client\Config\DrupalConfig;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "DatasetMigration"
 * )
 */
class DatasetMigration extends SourcePluginBase {

  protected $ids;

  public function initializeIterator() {
    $config = new DrupalConfig();
    $consumer = new Consumer($config);

    $datasets = $consumer->getDatasetsArray();
    return new \ArrayIterator($datasets);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $source = $row->getSource();
    $row->setSourceProperty('parameters', array_keys($source['dataset']->getParameters()));
    $row->setSourceProperty('columns', array_keys($source['dataset']->getColumns()));

    return parent::prepareRow($row);
  }

  /**
   * Returns available fields on the source.
   *
   * @return array
   *   Available fields in the source, keys are the field machine names as used
   *   in field mappings, values are descriptions.
   */
  public function fields() {
    return [
      'dataset_name' => t('Dataset name'),
    ];
  }

  public function __toString() {
    return t('DataTank migrate datasets');
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['dataset_name']['type'] = 'string';
    $ids['dataset_name']['alias'] = 'dataset_name';
    return $ids;
  }
}