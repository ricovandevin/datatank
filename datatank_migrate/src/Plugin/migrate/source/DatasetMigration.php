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

  public function initializeIterator() {
    $config = new DrupalConfig();
    $consumer = new Consumer($config, FALSE);

    $datasets = $consumer->getDatasetsArray();

    return new \ArrayIterator($datasets);
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $source = $row->getSource();
    $source['dataset']->merge_definition();

    $row->setSourceProperty('parameters', array_keys($source['dataset']->getParameters()));
    $row->setSourceProperty('columns', array_keys($source['dataset']->getColumns()));

    $fields = $source['dataset']->getFields();

    $tax_fields = datatank_helper_get_tax_fields();

    foreach ($tax_fields as $field_name => $dest) {
        $value = $fields[$dest]->getValue();
        if (is_array($value)) {
            $value = $value[0];
        }

        $raw_values = explode(',', trim($value));

      if (!empty($raw_values)) {
        $terms = [];
        foreach ($raw_values as $val) {
          if ($val) {
            $terms[] = datatank_migrate_create_term($field_name, trim($val));
          }
        }
        $row->setSourceProperty($dest, $terms);
      }
    }

    $row->setSourceProperty('documentation', trim($fields['userdocumentation_en']->getValue()));
    $row->setSourceProperty('title', trim($fields['title_en']->getValue()));

    $row->setSourceProperty('issued', strtotime($fields['issued']->getValue()));
    $row->setSourceProperty('modified', strtotime($fields['modified']->getValue()));

    if (isset($fields['wfs_uri'])) {
      $row->setSourceProperty('extra_links', [
        $fields['wfs_uri']->getValue(),
        $fields['wms_uri']->getValue(),
      ]);
    }


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