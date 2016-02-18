<?php

/**
 * @file
 * Command-line tools to aid performing and developing migrations.
 */
use Drupal\Component\Utility\Unicode;

/**
 * Implements hook_drush_command().
 */
function datatank_migrate_drush_command() {
  $items['datatank-generate-url'] = [
    'description' => 'generate all urls for datasets.',
    'aliases' => ['dgu'],
  ];

  return $items;
}

/**
 * @param string $migration_names
 */
function drush_datatank_migrate_datatank_generate_url() {
  $datasets = entity_load_multiple('datatank_dataset');
  $languages = ['ln', 'en'];

  $table = [];
  $table[] = ['Original path', 'alias'];
  foreach ($datasets as $dataset) {
    $system_path = '/datasets/' . $dataset->id();
    foreach ($languages as $language) {
      $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($system_path, $language);
      if ($path_alias == $system_path) {
        $alias = '/' . $dataset->getName();

        // No alias or old alias.
        $path = \Drupal::service('path.alias_storage')->save($system_path, $alias, $language);
        $table[] = [$system_path, $alias];
      }
    }
  }
  drush_print_table($table);
}
