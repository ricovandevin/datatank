<?php

class DatatankUIController extends EntityDefaultUIController {

  public function hook_menu() {
    $items = parent::hook_menu();

    $items['admin/content/datatank'] = $items[$this->path];
    $items['admin/content/datatank']['title'] = 'Datatank';
    $items['admin/content/datatank']['type'] = MENU_LOCAL_TASK;
    $items['admin/content/datatank']['description'] = t('Manage datatank');
    $items['admin/content/datatank']['access callback'] = 'datatank_dataset_access_callback';
    $items['admin/content/datatank']['access arguments'] = array('access content');

    $items[$this->path] = array(
      'title' => t('Datatanks'),
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => -66,
    );

    return $items;
  }

  public function overviewForm($form, &$form_state) {

    // Page me.
    $form['pager'] = array('#theme' => 'pager');

    $header = array(
      'title' => array('data' => t('Title'), 'field' => 'title'),
      'url' => array('data' => t('URL'), 'field' => 'url'),
      'last_sync' => array('data' => t('Last synchronized'), 'field' => 'url'),
      'edit' => array('data' => ''),
    );

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'datatank');

    // Check for sort order and sort key.
    if (!empty($_GET['sort']) && !empty($_GET['order'])) {
      foreach ($header as $field) {
        if ($_GET['order'] == $field['data']) {
          $query->propertyOrderBy($field['field'], $_GET['sort']);
          break;
        }
      }
    }

    $query->pager(10);

    $rows = array();
    $result = $query->execute();
    $datatanks = !empty($result['datatank']) ? datatank_load_multiple(array_keys($result['datatank'])) : array();
    foreach ($datatanks as $did => $datatank) {
      $rows['datatank_' . $did] = array(
        'title' => l($datatank->title, 'datatank/' . $did),
        'url' => l($datatank->url, $datatank->url),
        'last_sync' => empty($datatank->last_sync) ? t('Never') : format_date($datatank->last_sync, 'long'),
        'edit' => implode(str_repeat('&nbsp;', 5), array(
          l(t('edit'), 'datatank/' . $did . '/edit', array('query' => drupal_get_destination())),
          l(t('list datasets'), 'datatank/' . $did . '/datasets', array('query' => drupal_get_destination())),
        )),
      );
    }

    $form['entities'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#attributes' => array('class' => array('entity-sort-table')),
      '#empty' => t('No datatanks found.'),
    );

    return $form;
  }

}