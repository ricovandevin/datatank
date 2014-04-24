<?php

/**
 * @file
 * Custom controller for the Datatank dataset entity.
 */

class DatatankDatasetController extends EntityAPIController {

  /**
   * Override the save method.
   */
  public function save($entity, DatabaseTransaction $transaction = NULL) {

    // Changed and created.
    if (isset($entity->is_new)) {
      $entity->created = REQUEST_TIME;
    }
    $entity->changed = REQUEST_TIME;

    // Author.
    if (!isset($entity->uid)) {
      if (isset($GLOBALS['user']->uid)) {
        $entity->uid = $GLOBALS['user']->uid;
      }
      else {
        $entity->uid = 0;
      }
    }

    return parent::save($entity, $transaction);
  }

  /**
   * Override the buildContent method.
   */
  public function buildContent($dataset, $view_mode = 'full', $langcode = NULL, $content = array()) {

    $content['datatank_property'] = array(
      '#label' => t('Datatank'),
      '#value' => datatank_load($dataset->did)->url,
      '#theme' => 'datatank_property_field',
    );

    $content['identifier_property'] = array(
      '#label' => t('Identifier'),
      '#value' => $dataset->identifier,
      '#theme' => 'datatank_property_field',
      '#label_position' => 'inline'
    );

    $content['description_property'] = array(
      '#label' => t('Description'),
      '#value' => $dataset->description,
      '#theme' => 'datatank_property_field',
    );

    $content['date_property'] = array(
      '#label' => t('Date'),
      '#value' => $dataset->date,
      '#theme' => 'datatank_property_field',
    );

    $content['type_property'] = array(
      '#label' => t('Type'),
      '#value' => $dataset->type,
      '#theme' => 'datatank_property_field',
    );

    $content['format_property'] = array(
      '#label' => t('Format'),
      '#value' => $dataset->format,
      '#theme' => 'datatank_property_field',
    );

    $content['source_property'] = array(
      '#label' => t('Source'),
      '#value' => $dataset->source,
      '#theme' => 'datatank_property_field',
    );

    $content['data_language_property'] = array(
      '#label' => t('Data language'),
      '#value' => $dataset->data_language,
      '#theme' => 'datatank_property_field',
    );

    $content['rights_property'] = array(
      '#label' => t('Rights'),
      '#value' => $dataset->rights,
      '#theme' => 'datatank_property_field',
    );

    $content['links_property'] = array(
      '#label' => t('Download data'),
      '#value' => datatank_dataset_download_links($dataset),
      '#theme' => 'datatank_property_field',
    );

    return parent::buildContent($dataset, $view_mode, $langcode, $content);
  }

}
