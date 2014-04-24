<?php

/**
 * @file
 * Datatank Display Suite fields.
 */

/**
 * Implements hook_ds_fields_info().
 */
function datatank_ds_ds_fields_info($entity_type) {

  // Auto-generate dataset display suite fields, for properties that require
  // no rocket science for displaying the values.
  $dataset_fields = array(
    'date' => t('Date'),
    'type' => t('Type'),
    'format' => t('Format'),
    'source' => t('Source'),
    'data_language' => t('Data language'),
    'rights' => t('Rights'),
    'description' => t('Description'),
  );
  foreach ($dataset_fields as $field => $title) {
    $fields['datatank_dataset'][$field] = array(
      'title' => $title,
      'field_type' => DS_FIELD_TYPE_FUNCTION,
      'function' => 'datatank_ds_generic_property',
    );
  }

  // Datatank title.
  $fields['datatank']['title'] = array(
    'title' => t('Title'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'ds_render_field',
    'properties' => array(
      'entity_render_key' => 'title',
      'settings' => array(
        'link' => array('type' => 'select', 'options' => array('no', 'yes')),
        'wrapper' => array('type' => 'textfield', 'description' => t('Eg: h1, h2, p')),
        'class' => array('type' => 'textfield', 'description' => t('Put a class on the wrapper. Eg: block-title')),
      ),
      'default' => array('wrapper' => 'h2', 'link' => 0, 'class' => ''),
    ),
  );

  // Dataset title has similar functionality as datatank.
  $fields['datatank_dataset']['title'] = $fields['datatank']['title'];

  // Dataset url (= datatank url + dataset identifier)
  $fields['datatank_dataset']['url'] = array(
    'title' => t('Dataset URL'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'datatank_ds_dataset_url',
  );

  // More dataset info.
  $fields['datatank_dataset']['more'] = array(
    'title' => t('More info'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'datatank_ds_dataset_more',
  );

  // Dataset data links.
  $fields['datatank_dataset']['data_links'] = array(
    'title' => t('Data links'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'datatank_ds_dataset_data_links',
  );

  // Datatank url.
  $fields['datatank']['url'] = array(
    'title' => t('URL'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'datatank_ds_render_url',
    'properties' => array(
      'formatters' => array(
        'plain_text' => t('Plain text'),
        'link' => t('Link'),
      ),
    ),
  );

  // Datatank description.
  $fields['datatank']['description'] = array(
    'title' => t('Description'),
    'field_type' => DS_FIELD_TYPE_FUNCTION,
    'function' => 'datatank_ds_render_text_property',
  );


  if (isset($fields[$entity_type])) {
    return array($entity_type => $fields[$entity_type]);
  }

  return;
}
