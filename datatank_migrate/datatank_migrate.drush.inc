<?php

/**
 * @file
 * Command-line tools to aid performing and developing migrations.
 */
use Drupal\Component\Utility\Unicode;
use Drupal\tdt_client\Config\DrupalConfig;
use Drupal\tdt_client\Consumer;

/**
 * Implements hook_drush_command().
 */
function datatank_migrate_drush_command() {
  $items['datatank-generate-url'] = [
    'description' => 'generate all urls for datasets.',
    'aliases' => ['dgu'],
  ];

  $items['datatank-check-deleted-datasets'] = [
    'description' => 'Check if there are old datasets that needs to be removed from this cool website.',
    'aliases' => ['dcda'],
  ];

  return $items;
}

/**
 * @param string $migration_names
 */
function drush_datatank_migrate_datatank_generate_url() {
  $datasets = entity_load_multiple('datatank_dataset');
  $languages = ['nl', 'en'];

  $table = [];
  $table[] = ['Original path', 'alias'];
  foreach ($datasets as $dataset) {
    $system_path = '/datasets/' . $dataset->id();
    foreach ($languages as $language) {
      $path_alias = \Drupal::service('path.alias_manager')
        ->getAliasByPath($system_path, $language);
      if ($path_alias == $system_path) {
        $alias = '/' . $dataset->getName();

        // No alias or old alias.
        $path = \Drupal::service('path.alias_storage')
          ->save($system_path, $alias, $language);
        $table[] = [$system_path, $alias];
      }
    }
  }
  drush_print_table($table);
}

/**
 *
 */
function drush_datatank_migrate_datatank_check_deleted_datasets() {
  // Get datasets from API
  $config = new DrupalConfig();
  $consumer = new Consumer($config, FALSE);

  $datasets = array_keys($consumer->getDatasetsArray());

  // GET DATASETS FROM db
  $db = \Drupal::database();
  $names = $db->select('datatank_dataset_field_data', 'd')
    ->fields('d', ['name'])
    ->execute()
    ->fetchCol();


  $diff = array_diff($names, $datasets);

  if (!empty($diff)) {
    $table = [];
    $table[] = ['Dataset that are deleted'];

    foreach ($diff as $name) {
      $query = \Drupal::entityQuery('datatank_dataset');
      $query->condition('name', $name);
      $datasets = $query->execute();

      foreach ($datasets as $did) {
        $entity = entity_delete_multiple('datatank_dataset', [$did]);
        $table[] = [$name];
      }
    }
    drush_print_table($table);
  }
}
