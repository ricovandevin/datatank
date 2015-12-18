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
      foreach ($dataset->getParameterArray() as $key => $parameter) {
        if (!isset($parameters[strtolower($key)]) || $parameters[strtolower($key)]['default_value'] == '') {
          $parameters[strtolower($key)] = $parameter;
        }
      }
    }
    return new \ArrayIterator($parameters);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    //$row->setSourceProperty('default_value', $source->getProperty('default_value'));

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
      'documentation' => t('Documentation'),
      'default_value' => t('Default value')
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