<?php

/**
 * @file
 * Custom controller for the Datatank entity.
 */

class DatatankController extends EntityAPIController {

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
   * Override the delete method.
   *
   * Delete all datasets of this datatank.
   */
  public function delete($ids, DatabaseTransaction $transaction = NULL) {
    foreach ($ids as $did) {
      $datasets = datatank_dataset_load_by_datatank((int) $did);
      foreach ($datasets as $dataset) {
        entity_delete('datatank_dataset', $dataset->dsid);
      }
    }
    return parent::delete($ids, $transaction);
  }

}
