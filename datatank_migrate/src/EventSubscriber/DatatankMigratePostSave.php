<?php

/**
 * Migrate post row save event subscriber and handler.
 */

namespace Drupal\datatank_migrate\EventSubscriber;

// This is the interface we are going to implement.
use \Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Drupal\migrate\Event\MigrateEvents;
// This class contains the event we want to subscribe to.
use \Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Subscribe to MigrateEvents::POST_ROW_SAVE events.
 */
class DatatankMigratePostSave implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * Publish the Event.
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_ROW_SAVE][] = array('updateTranslations');
    return $events;
  }

  /**
   * MigrateEvents::POST_ROW_SAVE event handler.
   *
   */
  public function updateTranslations($event) {

    $tranlatable_fields = [
      'ParameterMigration' => [
        'entity_type' => 'datatank_parameter',
        'fields' => [
          'documentation' => [
            'type' => 'text',
            'key' => 'documentation_nl'
          ],
          'default_value' => [
            'type' => 'text',
            'key' => 'default_value'
          ],
        ]
      ],
      'DatasetMigration' => [
        'entity_type' => 'datatank_dataset',
        'fields' => [
          'field_dataset_title' => [
            'type' => 'longtext',
            'key' => 'title_nl'
          ],
          'field_dataset_userdocumentation' => [
            'type' => 'longtext',
            'key' => 'userdocumentation_nl'
          ]
        ]
      ],
    ];

    $row = $event->getRow();

    // These are defined in migration.yml.
    $available_languages = array('nl');

    $migrate_src_values = $row->getSource();

    if (isset($tranlatable_fields[$migrate_src_values['plugin']])) {
      $translatable_field = $tranlatable_fields[$migrate_src_values['plugin']];

      $migrated_node = $event->destinationIdValues[0];
      $entity = entity_load($translatable_field['entity_type'], $migrated_node);

      // Get multilingual fields in all languages.
      foreach ($available_languages as $lang) {
        if (!$entity->hasTranslation($lang)) {
          // Only add new translations, do not remove or update translations because of overwriting translation data.
          $values = [];

          foreach ($translatable_field['fields'] as $key => $value) {
            if (isset($migrate_src_values[$value['key']]) || isset($migrate_src_values['dataset']->getFields()[$value['key']])) {

              switch ($value['type']) {
                case "text" :
                default:
                  $values[$key] = $migrate_src_values[$value['key']];
                  break;

                case 'longtext':
                  $values[$key]['value'] = isset($migrate_src_values[$value['key']]) ? $migrate_src_values[$value['key']] : $migrate_src_values['dataset']->getFields()[$value['key']]->getValue();
                  break;
              }
            }
          }

          /* if ($entity->hasTranslation($lang)) {
            $entity->removeTranslation($lang);
            } */

          $translated_entity = $entity->addTranslation($lang, $values);
          $translated_entity->save();
        }
      }

      $map = $event->getMigration()->getIdMap();

      $map->saveIdMapping($event->getRow(), array($migrated_node));
    }
  }

}
