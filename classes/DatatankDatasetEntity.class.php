<?php

/**
 * @file
 * Datatank dataset custom entity class.
 */

class DatatankDatasetEntity extends Entity {
  /**
   * Override defaultUri().
   */
  protected function defaultUri() {
    return array('path' => 'dataset/' . $this->identifier());
  }
}
