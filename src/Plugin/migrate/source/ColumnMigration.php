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
 *   id = "ColumnMigration"
 * )
 */
class ColumnMigration extends SourcePluginBase {

  protected $ids;

  public function initializeIterator() {
    $config = new DrupalConfig();
    $consumer = new Consumer($config);

    $datasets = $consumer->getDatasets();
    $columns = [];
    foreach ($datasets as $dataset) {
      $columns = array_merge($dataset->getColumnsArray(), $columns);
    }
    return new \ArrayIterator($columns);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    /*// Get Field API field values.
    foreach (array_keys($this->getFields('node', $row->getSourceProperty('type'))) as $field) {
      $nid = $row->getSourceProperty('nid');
      $vid = $row->getSourceProperty('vid');
      $row->setSourceProperty($field, $this->getFieldValues('node', $field, $nid, $vid));
    }*/
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
      'column_name' => t('Column name'),
      'is_pk' => t('Is Primary key'),
      'column_name_alias' => t('Column name alias'),
      'index' => t('Index'),
      'documentation' => t('Documentation')
    ];
  }

  public function __toString() {
    return t('DataTank migrate unique columns');
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['column_name']['type'] = 'string';
    $ids['column_name']['alias'] = 'column_name';
    return $ids;
  }
}