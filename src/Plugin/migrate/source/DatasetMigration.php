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
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;

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

    /*$vocabulary = Vocabulary::load('field_dataset_category');
    $term = Term::create([
      'name' => 'Lama',
      'vid' => $vocabulary->id()
    ]);
    $term->save();*/


    $row->setSourceProperty('category', 1);
    $row->setSourceProperty('type', 'hallowa');

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