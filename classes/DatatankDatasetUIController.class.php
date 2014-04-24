<?php

class DatatankDatasetUIController extends EntityDefaultUIController {

  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['title'] = t('Datasets');
    $items[$this->path]['description'] = t('Manage datasets');
    $items[$this->path]['access callback'] = 'datatank_dataset_access_callback';
    $items[$this->path]['access arguments'] = array('access content');
    $items[$this->path]['type'] = MENU_LOCAL_TASK;
    return $items;
  }

  /**
   * Admin form for datasets.
   */
  public function overviewForm($form, &$form_state) {

    // Page me.
    $form['pager'] = array('#theme' => 'pager');

    $header = array(
      'title' => array('data' => t('Title'), 'field' => 'title'),
      'datatank' => array('data' => t('Datatank'), 'field' => 'datatank'),
      'identifier' => array('data' => t('Identifier'), 'field' => 'identifier'),
      'status' => array('data' => t('Status'), 'field' => 'status'),
      'orphaned' => array('data' => t('Orphaned'), 'field' => 'orphaned'),
      'edit' => array('data' => ''),
    );

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'datatank_dataset');

    // Check for sort order and sort key.
    if (!empty($_GET['sort']) && !empty($_GET['order'])) {
      foreach ($header as $field) {
        if ($_GET['order'] == $field['data'] && $_GET['order'] != t('Datatank')) {
          $query->propertyOrderBy($field['field'], $_GET['sort']);
          break;
        }
      }
    }

    $query->pager(20);

    $rows = array();
    $result = $query->execute();
    $datasets = !empty($result['datatank_dataset']) ? datatank_dataset_load_multiple(array_keys($result['datatank_dataset'])) : array();
    foreach ($datasets as $dsid => $dataset) {
      $rows['dataset_' . $dsid] = array(
        'title' => l($dataset->title, 'dataset/' . $dsid),
        'datatank' => datatank_dataset_datatank_getter($dataset),
        'identifier' => $dataset->identifier,
        'status' => $dataset->status ? t('Published') : t('Unpublished'),
        'orphaned' => $dataset->orphaned ? t('Orphaned') : t('Not orphaned'),
        'edit' => l(t('edit'), 'dataset/' . $dsid . '/edit', array('query' => drupal_get_destination())),
      );
    }

    if (!empty($_GET['order'])) {
      if ($_GET['order'] == t('Datatank')) {
        if (strtoupper($_GET['sort']) == 'ASC') {
          usort($rows, function ($a, $b) {
            return strcmp($a['datatank'], $b['datatank']);
          });
        }
        else {
          usort($rows, function ($a, $b) {
            return strcmp($b['datatank'], $a['datatank']);
          });
        }
      }
    }

    $form['bulk_operations'] = array(
      '#type' => 'fieldset',
      '#title' => t('Bulk Operations'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );

    $form['bulk_operations']['operations'] = array(
      '#type' => 'select',
      '#options' => array(
        0 => t('Select a bulk operation'),
        'publish' => t('Publish datasets'),
        'unpublish' => t('Unpublish datasets'),
      ),
    );

    $form['bulk_operations']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );

    $form['entities'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#attributes' => array('class' => array('entity-sort-table')),
      '#empty' => t('No datasets. Add a datatank and/or synchronize it.'),
    );

    return $form;
  }

  /**
   * Form Submit method.
   */
  public function overviewFormSubmit($form, &$form_state) {
    $values = $form_state['input'];
    $dsids = array();

    if (!empty($values['entities'])) {
      foreach ($values['entities'] as $value) {
        if (!empty($value)) {
          $dsids[] = str_replace('dataset_', '', $value);
        }
      }

      if (!empty($values['operations'])) {
        $status = $values['operations'] == 'publish' ? 1 : 0;
        $datasets = datatank_dataset_load_multiple($dsids);
        foreach ($datasets as $dataset) {
          $dataset->status = $status;
          entity_save('datatank_dataset', $dataset);
        }
      }
    }
  }

}