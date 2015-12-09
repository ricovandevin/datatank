<?php

/**
 * @file
 * Contains Drupal\datatank\Form\DatasetSettingsForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\tdt_client\Consumer;
use Drupal\tdt_client\Config\DrupalConfig;

/**
 * Class ColumnSettingsForm.
 *
 * @ingroup datatank
 */
class DatasetSettingsForm extends FormBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * The config manager.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $configManager;

  /**
   * Constructs a new DatasetSettingsForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct() {

    $container = \Drupal::getContainer();

    $this->entityManager = new EntityManager();
    $this->entityManager->setContainer($container);

    $this->configManager = \Drupal::configFactory()
      ->getEditable('datatank.settings');
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'datatank_dataset_settings';
  }

  /**
   * Define the form used for ContentEntityExample settings.
   * @return array
   *   Form definition array.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $created_fields = $this->getImportedFields();

    $config = new DrupalConfig();
    $consumer = new Consumer($config);

    $fields = $consumer->getUniqueFields('field_dataset_');

    $form['created_fields'] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . t('Already created fields') . '</h1><div> ' . implode(', ', array_keys($created_fields)) . '</div>',
    ];

    $form['choose_columns'] = [
      '#prefix' => '<h1>Choose the fields you also want to import</h1>',
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    foreach ($fields as $field_name => $label) {
      if (!in_array($field_name, $created_fields)) {
        $form['choose_columns'][$field_name] = array(
          '#title' => $label,
          '#type' => 'select',
          '#options' => array(
            0 => "Don't Import",
            'string' => t('Textfield'),
            'taxonomy' => t('Taxonomy terms')
          ),
        );
      }
    }

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $created_fields = [];
    $field_storage_definitions = $this->entityManager->getFieldStorageDefinitions('datatank_dataset');

    foreach ($values['choose_columns'] as $field_name => $type) {
      if ($type) {

        if (!isset($field_storage_definitions[$field_name])) {
          switch ($type) {
            case "string" :
              datatank_helper_create_textfield($this->entityManager, $field_name);
              break;

            case "taxonomy" :
              datatank_helper_create_taxonomyfield('datatank_dataset', 'datatank_dataset', $field_name, $field_name, 'taxonomy_term', 'default', -1);
              break;
          }

          $created_fields[$field_name] = $field_name;
        }
      }
      /* else {
         //$entity = $this->entityManager->loadEntityByUuid('datatank_dataset', $field_storage_definitions[$field_name]->get('uuid'));

         //$entity->delete();
         //field_purge_batch(10);
       }*/
    }

    $this->configManager->set('enabled_dataset_fields', $created_fields);
    $this->configManager->save();
  }

  /**
   * Get the imported fields
   */
  public function getImportedFields() {
    $field_storage_definitions = $this->entityManager->getFieldStorageDefinitions('datatank_dataset');
    $created_fields = $this->configManager->get('enabled_dataset_fields');
    foreach ($created_fields as $field_name) {
      if (!isset($field_storage_definitions[$field_name])) {
        // Field was deleted
        unset($created_fields[$field_name]);
      }
    }

    return $created_fields;
  }

}

?>