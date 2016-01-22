<?php

namespace Drupal\datatank\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Tests the Search function /datasets and his facets
 *
 * @group datatank
 */
class DatatankSearchWebTest extends WebTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array(
    'views',
    'facets',
    'search_api',
    'config',
    'locale',
    'datatank',
    'datatank_migrate',
  );

  protected $profile = 'datatankprofile';


  /**
   * A simple user with 'access content' permission
   */
  private $user;

  /**
   * Perform any initial set up tasks that run before every test method
   */
  public function setUp() {
    parent::setUp();

    // Import the content of the sync directory.
    $sync = \Drupal::service('config.storage.sync');
    $source = new FileStorage('config/sync');

    $this->copyConfig($source, $sync);

    $datatank_site = $this->config('system.site')->get();
    $sync->write('system.site', $datatank_site);

    $this->configImporter()->import();
  }

  public function testSearch() {
    $this->drupalGet('datasets');

    $this->assertText('datasets found', "Found 'datasets found'.");

  }
}