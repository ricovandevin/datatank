<?php

/**
 * @file
 * Contains \Drupal\datatank\simpletest\Functional\DatatankTest.
 */

namespace Drupal\datatank\Tests\Functional;

use Drupal\simpletest\BrowserTestBase;

/**
 * Tests BrowserTestBase functionality.
 *
 * @group simpletest
 */
class DatatankTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array();

  /**
   * Tests basic page test.
   */
  public function testGoTo() {
    $this->drupalGet('/datasets');
    $this->assertSession()->statusCodeEquals(200);
    // Test page contains some text.
    //$this->assertSession()->pageTextContains('Test page text.');
  }


}
