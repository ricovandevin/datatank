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
 *   id = "ColumnMigration"
 * )
 */
class ColumnMigration extends SourcePluginBase {

  protected $ids;

  public function initializeIterator() {
    $config = new DrupalConfig();
    $consumer = new Consumer($config, FALSE);

    $datasets = $consumer->getDatasets();
    $columns = [];
    foreach ($datasets as $dataset) {
      //$columns = array_merge($dataset->getColumnsArray(), $columns);
      foreach ($dataset->getColumnsArray() as $key => $column) {
        // There are capitalized columns in the datatank api.
        if (!isset($columns[strtolower($key)]) || $columns[strtolower($key)]['documentation'] == '') {
          $columns[strtolower($key)] = $column;
        }
      }
    }
    return new \ArrayIterator($columns);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $row->setSourceProperty('documentation', trim($row->getSourceProperty('documentation')));

    return parent::prepareRow($row);
  }

  /**
   * Returns available fields on the source.
   *
   * @return array$row->setSourceProperty('langcode', ['en', 'nl']);
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