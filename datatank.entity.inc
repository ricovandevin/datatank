<?php

/**
 * Implements hook_entity_info().
 */
function datatank_entity_info(){
  $entity_info = array();

  $entity_info['datatank'] = array(
    'label' => 'Datatank',
    'label callback' => 'datatank_label_callback',
    'entity class' => 'DatatankEntity',
    'controller class' => 'DatatankController',
    'base table' => 'datatank',
    'fieldable' => TRUE,
    'entity keys' => array(
      'id' => 'did',
    ),
    'load hook' => 'datatank_load',
    'uri callback' => 'datatank_uri_callback',
    'static cache' => TRUE,
    'module' => 'datatank',
    'access callback' => 'datatank_access_callback',
    'bundles' => array(
      'datatank' => array(
        'label' => 'Datatank',
        'admin' => array(
          'path' => 'admin/structure/datatank/manage',
          'access arguments' => array('administer datatank entities'),
        ),
      )
    ),
    'admin ui' => array(
      'path' => 'admin/content/datatank/datatank',
      'controller class' => 'DatatankUIController',
      'file' => 'datatank.admin.inc',
    ),
    'view modes' => array(
      'full' => array(
        'label' => t('Full content'),
        'custom settings' => FALSE,
      ),
      'teaser' => array(
        'label' => t('Teaser'),
        'custom settings' => TRUE,
      ),
    ),
  );

  $entity_info['datatank_dataset'] = array(
    'label' => 'Dataset',
    'label callback' => 'datatank_dataset_label_callback',
    'entity class' => 'DatatankDatasetEntity',
    'controller class' => 'DatatankDatasetController',
    'base table' => 'datatank_dataset',
    'fieldable' => TRUE,
    'entity keys' => array(
      'id' => 'dsid',
    ),
    'load hook' => 'datatank_dataset_load',
    'uri callback' => 'datatank_dataset_uri_callback',
    'static cache' => TRUE,
    'module' => 'datatank',
    'access callback' => 'datatank_dataset_access_callback',
    'bundles' => array(
      'datatank_dataset' => array(
        'label' => 'Dataset',
        'admin' => array(
          'path' => 'admin/structure/datatank_dataset/manage',
          'access arguments' => array('administer dataset entities'),
        ),
      )
    ),
    'admin ui' => array(
      'path' => 'admin/content/datatank/dataset',
      'controller class' => 'DatatankDatasetUIController',
      'file' => 'datatank.admin.inc',
    ),
    'view modes' => array(
      'full' => array(
        'label' => t('Full content'),
        'custom settings' => FALSE,
      ),
      'teaser' => array(
        'label' => t('Teaser'),
        'custom settings' => TRUE,
      ),
    ),
  );

  return $entity_info;
}

/**
 * Implements hook_entity_property_info().
 */
function datatank_entity_property_info() {
  $info = array();

  $datatank = &$info['datatank']['properties'];

  $datatank['did'] = array(
    'label' => t('Datatank ID'),
    'type' => 'integer',
    'description' => t('Unique id for the datatank.'),
    'schema field' => 'did',
  );

  $datatank['uid'] = array(
    'label' => t('User ID'),
    'type' => 'user',
    'description' => t('The user that created the datatank.'),
    'schema field' => 'uid',
  );

  $datatank['created'] = array(
    'label' => t('Date created'),
    'type' => 'date',
    'description' => t('The date the datatank was created.'),
    'schema field' => 'created',
  );

  $datatank['changed'] = array(
    'label' => t('Date changed'),
    'type' => 'date',
    'schema field' => 'changed',
    'description' => t('The date the datatank last changed.'),
  );

  $datatank['title'] = array(
    'label' => t('Title'),
    'type' => 'text',
    'description' => t('The title of the datatank.'),
    'schema field' => 'title',
  );

  $datatank['description'] = array(
    'label' => t('Description'),
    'type' => 'text',
    'description' => t('The description of the datatank.'),
    'schema field' => 'description',
  );

  $datatank['url'] = array(
    'label' => t('URL'),
    'type' => 'uri',
    'description' => t('The URL of the datatank.'),
    'schema field' => 'url',
  );

  $datatank['sync_frequency'] = array(
    'label' => t('Synchronize frequency'),
    'type' => 'integer',
    'description' => t('The number of seconds to pass before the next sync. If this is 0, the sync should happen manually.'),
    'schema field' => 'sync_frequency',
  );

  $datatank['publish_new'] = array(
    'label' => t('Auto-publish new datasets'),
    'type' => 'boolean',
    'description' => t('Auto-publish new datasets found on sync.'),
    'schema field' => 'publish_new',
  );

  $datatank['delete_orphaned'] = array(
    'label' => t('Delete orphaned datasets'),
    'type' => 'boolean',
    'description' => t('Automatically delete ophaned dataset, instead of just depublishing them.'),
    'schema field' => 'delete_orphaned',
  );

  $datatank['last_sync'] = array(
    'label' => t('Last synchronized on'),
    'type' => 'integer',
    'description' => t('The timestamp of when the datatank was last synchronized.'),
    'schema field' => 'last_sync',
  );

  $dataset = &$info['datatank_dataset']['properties'];

  $dataset['dsid'] = array(
    'label' => t('Dataset ID'),
    'type' => 'integer',
    'description' => t('Unique id for the dataset.'),
    'schema field' => 'dsid',
  );

  $dataset['uid'] = array(
    'label' => t('User ID'),
    'type' => 'user',
    'description' => t('The user that created the dataset.'),
    'schema field' => 'uid',
  );

  $dataset['created'] = array(
    'label' => t('Date created'),
    'type' => 'date',
    'description' => t('The date the dataset was created.'),
    'schema field' => 'created',
  );

  $dataset['changed'] = array(
    'label' => t('Date changed'),
    'type' => 'date',
    'schema field' => 'changed',
    'description' => t('The date the dataset last changed.'),
  );

  $dataset['did'] = array(
    'label' => t('Datatank ID'),
    'type' => 'datatank',
    'description' => t('The ID of the datatank this dataset is a part of.'),
    'schema field' => 'did',
  );

  $dataset['identifier'] = array(
    'label' => t('Identifier'),
    'type' => 'text',
    'description' => t('The identifier of the dataset.'),
    'schema field' => 'identifier',
  );

  $dataset['description'] = array(
    'label' => t('Description'),
    'type' => 'text',
    'description' => t('The description of the dataset.'),
    'schema field' => 'description',
  );

  $dataset['title'] = array(
    'label' => t('Title'),
    'type' => 'text',
    'description' => t('The title of the dataset.'),
    'schema field' => 'title',
  );

  $dataset['date'] = array(
    'label' => t('Date'),
    'type' => 'text',
    'description' => t('A point or period of time associated with an event in the lifecycle of the dataset.'),
    'schema field' => 'date',
  );

  $dataset['type'] = array(
    'label' => t('Type'),
    'type' => 'text',
    'description' => t('The nature or genre of the dataset.'),
    'schema field' => 'type',
  );

  $dataset['format'] = array(
    'label' => t('Format'),
    'type' => 'text',
    'description' => t('The file format, physical medium, or dimensions of the dataset.'),
    'schema field' => 'format',
  );

  $dataset['source'] = array(
    'label' => t('Source'),
    'type' => 'text',
    'description' => t('A related resource from which the described dataset is derived.'),
    'schema field' => 'source',
  );

  $dataset['data_language'] = array(
    'label' => t('Dataset language'),
    'type' => 'text',
    'description' => t('The language of the dataset.'),
    'schema field' => 'data_language',
  );

  $dataset['rights'] = array(
    'label' => t('Rights'),
    'type' => 'text',
    'description' => t('Information about rights held in and over the dataset.'),
    'schema field' => 'rights',
  );

  $dataset['status'] = array(
    'label' => t('Status'),
    'type' => 'boolean',
    'description' => t('Published or unpublished.'),
    'schema field' => 'status',
  );

  $dataset['orphaned'] = array(
    'label' => t('Orphaned'),
    'type' => 'boolean',
    'description' => t('If the dataset was not found anymore in the datatank, this is set to TRUE.'),
    'schema field' => 'orphaned',
  );

  $dataset['url'] = array(
    'label' => t('URL'),
    'type' => 'uri',
    'description' => t('Link to the dataset.'),
    'getter callback' => 'datatank_dataset_url_getter',
    'computed' => TRUE,
    'entity views field' => TRUE,
  );

  $dataset['datatank'] = array(
    'label' => t('Datatank'),
    'type' => 'text',
    'description' => t('Name of the dataset.'),
    'getter callback' => 'datatank_dataset_datatank_getter',
    'computed' => TRUE,
    'entity views field' => TRUE,
  );

  return $info;
}

function datatank_dataset_url_getter($dataset) {
  $datatank = datatank_load($dataset->did);
  return $datatank->url . '/' . $dataset->identifier;
}

function datatank_dataset_datatank_getter($dataset) {
  $datatank = datatank_load($dataset->did);
  return $datatank->title;
}

/**
 * Provide a label based on the properties for an entity.
 */
function datatank_label_callback($datatank, $type) {
  if (!empty($datatank->title)) {
    return $datatank->title;
  }
  return 'Datatank';
}

/**
 * Provide a label based on the properties for an entity.
 */
function datatank_dataset_label_callback($dataset, $type) {
  if (!empty($dataset->title)) {
    return $dataset->title;
  }
  return 'Dataset';
}

/**
 * Access callback for datatank entity.
 */
function datatank_access_callback() {
  return user_access('administer datatank entities');
}

/**
 * Access callback for datatank dataset entity.
 */
function datatank_dataset_access_callback() {
  return user_access('administer dataset entities');
}

/**
 * Load hook for a datatank entity.
 */
function datatank_load($did, $reset = FALSE) {
  $datatanks = datatank_load_multiple(array($did), array(), $reset);
  return reset($datatanks);
}

/**
 * Load hook for a datatank entity.
 */
function datatank_dataset_load($dsid, $reset = FALSE) {
  $datasets = datatank_dataset_load_multiple(array($dsid), array(), $reset);
  return reset($datasets);
}

/**
 * Load dataset by datatank, identifier.
 */
function datatank_dataset_load_by_identifier($did = NULL, $identifier = '') {
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'datatank_dataset');
  $query->propertyCondition('did', $did);
  $query->propertyCondition('identifier', $identifier);
  $result = $query->execute();
  if (isset($result['datatank_dataset'])) {
    return array_shift(datatank_dataset_load_multiple(array_keys($result['datatank_dataset'])));
  }
  return FALSE;
}

/**
 * Load dataset by datatank.
 */
function datatank_dataset_load_by_datatank($did = 0) {
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'datatank_dataset');
  $query->propertyCondition('did', $did);
  $result = $query->execute();
  if (isset($result['datatank_dataset'])) {
    return datatank_dataset_load_multiple(array_keys($result['datatank_dataset']));
  }
  return FALSE;
}

/**
 * Load multiple datatank entities.
 */
function datatank_load_multiple($dids = array(), $conditions = array(), $reset = FALSE) {
  return entity_load('datatank', $dids, $conditions, $reset);
}

/**
 * Load multiple datatank entities.
 */
function datatank_dataset_load_multiple($dsids = array(), $conditions = array(), $reset = FALSE) {
  return entity_load('datatank_dataset', $dsids, $conditions, $reset);
}

/**
 * Get all datatanks.
 */
function datatank_load_all() {
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'datatank');
  $query->propertyOrderBy('title', 'asc');
  $result = $query->execute();
  if (isset($result['datatank'])) {
    return datatank_load_multiple(array_keys($result['datatank']));
  }
  return array();
}

/**
 * Entity URI callback.
 */
function datatank_uri_callback($datatank) {
  return array('path' => 'datatank/' . $datatank->did);
}

/**
 * Entity URI callback.
 */
function datatank_dataset_uri_callback($dataset) {
  return array('path' => 'dataset/' . $dataset->dsid);
}

/**
 * View for datatank/%datatank.
 */
function datatank_view_entity($datatank) {
  drupal_set_title($datatank->title);
  return entity_get_controller('datatank')->view(array($datatank->did => $datatank));
}

/**
 * View for dataset/%datatank_dataset.
 */
function datatank_dataset_view_entity($dataset) {
  drupal_set_title($dataset->title);
  return entity_get_controller('datatank_dataset')->view(array($dataset->dsid => $dataset));
}
