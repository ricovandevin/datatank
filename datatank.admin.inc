<?php

/**
 * Form that lists all datasets for a given datatank, with edit options.
 */
function datatank_dataset_list_form($form, &$form_state, $datatank) {

  // Get all datasets for this datatank.
  $datasets = datatank_dataset_load_by_datatank($datatank->did);
  foreach ($datasets as $dsid => $dataset) {
    $title = l($dataset->title, 'dataset/' . $dsid) . '<br>' . $datatank->url . '/' . $dataset->identifier;
    if ($dataset->orphaned) {
      $title = '<s>' . $title . '</s> <strong>Orphaned</strong>';
    }
    $form['dataset-' . $dsid] = array(
      '#type' => 'checkbox',
      '#title' => $title,
      '#default_value' => $dataset->status,
      '#disabled' => $dataset->orphaned,
    );
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save dataset statuses'),
  );

  $form_state['storage']['did'] = $datatank->did;
  $form['sync_now'] = array(
    '#type' => 'submit',
    '#value' => t('Synchronize now'),
  );

  return $form;
}

/**
 * Sychrnonize the datatank from the form with the list of datasets.
 */
function datatank_dataset_list_form_sync_now(&$form, &$form_state) {
  if ($form_state['triggering_element']['#value'] == t('Synchronize now')) {
    datatank_synchronize_datasets($form_state['storage']['did']);
  }
}

/**
 * Submit callback for the dataset list form.
 */
function datatank_dataset_list_form_submit(&$form, &$form_state) {
  if ($form_state['triggering_element']['#value'] == t('Synchronize now')) {
    datatank_synchronize_datasets($form_state['storage']['did']);
  }
  else {
    foreach ($form_state['values'] as $key => $value) {
      if (strpos($key, 'dataset-') !== FALSE) {
        list(, $dsid) = explode('dataset-', $key);
        $dataset = datatank_dataset_load($dsid);
        if ($dataset->status != $value) {
          drupal_set_message(t('Changed status for %dataset from @prev to @cur.', array('%dataset' => $dataset->title, '@prev' => $dataset->status ? t('published') : t('unpublished'), '@cur' => $value ? t('published') : t('unpublished'))));
          $dataset->status = $value;
          entity_save('datatank_dataset', $dataset);
        }
      }
    }
  }
}

/**
 * Landing page for the 'manage datatanks' page.
 */
function datatank_admin_info_page() {
  return t('Manage datatank fields and displays');
}

/**
 * Landing page for the 'manage datatanks' page.
 */
function datatank_dataset_admin_info_page() {
  return t('Manage dataset fields and displays');
}

/**
 * Create and edit entity form of the datatank entity.
 */
function datatank_form($form, &$form_state, $datatank) {
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#default_value' => !empty($datatank->title) ? $datatank->title : '',
    '#required' => TRUE,
  );

  $form['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => !empty($datatank->description) ? $datatank->description : '',
  );

  $form['url'] = array(
    '#type' => 'textfield',
    '#title' => t('URL'),
    '#default_value' => !empty($datatank->url) ? $datatank->url : '',
    '#description' => t('The URL on which the datatank can be reached. Example: http://data.appreciate.be'),
    '#required' => TRUE,
  );

  // Add all the fields added through Field UI.
  field_attach_form('datatank', $datatank, $form, $form_state);

  // Additional settings.
  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
  );

  // Authentication.
  $form['auth_options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Authentication'),
    '#group' => 'additional_settings',
  );

  $form['auth_options']['auth_user'] = array(
    '#type' => 'textfield',
    '#title' => t('Username'),
    '#default_value' => !empty($datatank->auth_user) ? $datatank->auth_user : '',
    '#size' => 30,
  );

  $form['auth_options']['auth_pass'] = array(
    '#type' => 'textfield',
    '#title' => t('Password'),
    '#default_value' => !empty($datatank->auth_pass) ? $datatank->auth_pass : '',
    '#size' => 25,
  );

  // Synchronize options.
  $form['sync_options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Synchronize'),
    '#group' => 'additional_settings',
  );

  $form['sync_options']['sync_frequency'] = array(
    '#type' => 'select',
    '#title' => t('Synchronize every'),
    '#options' => array(
      0 => t('never (manually sync)'),
      300 => t('5 minutes'),
      900 => t('15 minutes'),
      1800 => t('half hour'),
      3600 => t('hour'),
      7200 => t('2 hours'),
      21600 => t('6 hours'),
      86400 => t('day'),
      172800 => t('two days'),
      604800 => t('week'),
      2592000 => t('month'),
    ),
    '#description' => t('We use cron to automatically synchronize datasets, so the synchronize frequency should be equal of greater than the cron frequency.'),
    '#default_value' => !empty($datatank->sync_frequency) ? $datatank->sync_frequency : 0,
  );

  $form['sync_options']['publish_new'] = array(
    '#type' => 'checkbox',
    '#title' => t('Publish new datasets'),
    '#description' => t('When this datatank is synchronized and new datasets are found, automatically set them as published.'),
    '#default_value' => !empty($datatank->publish_new) ? $datatank->publish_new : 1,
  );

  $form['sync_options']['delete_orphaned'] = array(
    '#type' => 'checkbox',
    '#title' => t('Delete orphaned datasets'),
    '#description' => t("When this datatank is synchronized and some datasets are not found, they are automatically unpublished.
                         If this option is checked, they are deleted. <strong>You will lose any data (fields) you've added to those datasets."),
    '#default_value' => !empty($datatank->delete_orphaned) ? $datatank->delete_orphaned : 1,
  );

  // Create a 'delete button'.
  $delete_link = '';
  if (!empty($datatank->did)) {
    $delete_link = l(
      t('Delete datatank'),
      'admin/content/datatank/manage/' . $datatank->did . '/delete',
      array(
        'query' => array('destination' => 'admin/content/datatank'),
        'attributes' => array('class' => array('button remove')),
      )
    );
  }

  // Provide update/save and delete buttons.
  $form['actions'] = array(
    '#type' => 'actions',
    'submit' => array(
      '#type' => 'submit',
      '#value' => !empty($datatank->did) ? t('Update datatank') : t('Save datatank'),
    ),
    'delete' => array(
      '#markup' => $delete_link,
    ),
  );

  return $form;
}

/**
 * Validation handler for the create and edit entity form of the datatank entity.
 */
function datatank_form_validate(&$form, &$form_state) {
  // The provided URL must be a valid one.
  if (!valid_url($form_state['values']['url'], TRUE)) {
    form_set_error('url', t('The URL must be valid and absolute, like http://data.example.com'));
  }
  else {
    // The url is properly formatted, but it must be reachable.
    $response = drupal_http_request($form_state['values']['url']);
    if (empty($response->status_message) || $response->status_message != 'OK') {
      form_set_error('url', t('The provided URL could not be reached.<br>Error message: @error', array('@error' => $response->error)));
    }
  }
}

/**
 * Submit handler for the create and edit entity form of the datatank entity.
 */
function datatank_form_submit(&$form, &$form_state) {
  $datatank = entity_ui_form_submit_build_entity($form, $form_state);
  $datatank->save();
  drupal_set_message(t('Datatank <em>@datatank</em> has been succesfully saved.', array('@datatank' => $datatank->title)));
  if ($datatank->created == $datatank->changed) {
    datatank_synchronize_datasets($datatank->did);
  }
  $form_state['redirect'] = 'datatank/' . $datatank->did;
}

/**
 * Create and edit entity form of the datatank dataset entity.
 */
function datatank_dataset_form($form, &$form_state, $dataset) {
  // Add all the fields added through Field UI.
  field_attach_form('datatank_dataset', $dataset, $form, $form_state);

  // Create a 'delete button'.
  $delete_link = '';
  if (!empty($dataset->dsid)) {
    $delete_link = l(
      t('Delete dataset'),
      'admin/content/datatank_dataset/manage/' . $dataset->dsid . '/delete',
      array(
        'query' => array('destination' => 'admin/content/datatank_dataset'),
        'attributes' => array('class' => array('button remove')),
      )
    );
  }

  // Provide update/save and delete buttons.
  $form['actions'] = array(
    '#type' => 'actions',
    'submit' => array(
      '#type' => 'submit',
      '#value' => !empty($dataset->dsid) ? t('Update dataset') : t('Save dataset'),
    ),
    'status_change' => array(
      '#type' => 'submit',
      '#value' => $dataset->status ? t('Unpublish dataset') : t('Publish dataset'),
    ),
  );

  return $form;
}

/**
 * Submit handler for the create and edit entity form of the datatank dataset entity.
 */
function datatank_dataset_form_submit(&$form, &$form_state) {
  $dataset = entity_ui_form_submit_build_entity($form, $form_state);

  if ($form_state['triggering_element']['#value'] == t('Unpublish dataset') || $form_state['triggering_element']['#value'] == t('Publish dataset')) {
    $dataset->status = $form_state['triggering_element']['#value'] == t('Publish dataset');
  }

  $dataset->save();
  drupal_set_message(t('Dataset <em>@dataset</em> has been succesfully saved.', array('@dataset' => $dataset->title)));
  $form_state['redirect'] = 'dataset/' . $dataset->dsid;
}


