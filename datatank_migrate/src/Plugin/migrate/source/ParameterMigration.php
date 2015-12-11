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
 *   id = "ParameterMigration"
 * )
 */
class ParameterMigration extends SourcePluginBase {

  protected $ids;

  public function initializeIterator() {
    $config = new DrupalConfig();
    $consumer = new Consumer($config);

    $datasets = $consumer->getDatasets();
    $parameters = [];
    foreach ($datasets as $dataset) {
      $parameters = array_merge($dataset->getParameterArray(), $parameters);
    }
    return new \ArrayIterator($parameters);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
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
      'param_name' => t('Parameter unique name'),
      'required' => t('Required field'),
      'documentation' => t('Documentation')
    ];
  }

  public function __toString() {
    return t('DataTank migrate unique parameters');
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['param_name']['type'] = 'string';
    $ids['param_name']['alias'] = 'param_name';
    return $ids;
  }
}