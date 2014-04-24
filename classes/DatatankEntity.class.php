<?php

/**
 * @file
 * Datatank custom entity class.
 */

class DatatankEntity extends Entity {
  /**
   * Override defaultUri().
   */
  protected function defaultUri() {
    return array('path' => 'datatank/' . $this->identifier());
  }
}
