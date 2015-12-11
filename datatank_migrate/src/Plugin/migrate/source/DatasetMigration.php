<?php
/**
 * @file
 * Contains \Drupal\datatank_migrate\Plugin\migrate\source
 */
namespace Drupal\datatank_migrate\Plugin\migrate\source;

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

  /*public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }*/

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

    $fields = $source['dataset']->getFields();

    $category = datatank_migrate_create_term('field_dataset_category', $fields['category']->getValue());

    $row->setSourceProperty('category', $category);

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
      'type' => 'jaja'
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