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
    $allowed_types = $this->configManager->get('enabled_dataset_fields');
    dsm($allowed_types);
    $form['choose_columns'] = array(
      '#title' => 'Choose your datasets',
      '#type' => 'checkboxes',
      '#options' => [
        'field_dataset_type' => 'type',
        'field_dataset_licence' => 'licence'
      ],
      '#default_value' => $allowed_types
    );

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

    $field_storage_definitions = $this->entityManager->getFieldStorageDefinitions('datatank_dataset');

    foreach ($values['choose_columns'] as $field_name => $active) {
      if ($active) {
        if (!isset($field_storage_definitions[$field_name])) {
          $field_storage_values = [
            'field_name' => $field_name,
            'entity_type' => 'datatank_dataset',
            'type' => 'string',
            'translatable' => TRUE,
          ];

          $field_values = [
            'field_name' => $field_name,
            'entity_type' => 'datatank_dataset',
            'bundle' => 'datatank_dataset',
            'label' => $field_name,
            'translatable' => TRUE,
          ];

          $this->entityManager->getStorage('field_storage_config')
            ->create($field_storage_values)
            ->save();

          $field = $this->entityManager->getStorage('field_config')
            ->create($field_values);
          $field->save();

          entity_get_form_display('datatank_dataset', 'datatank_dataset', 'default')
            ->setComponent($field_name, [])
            ->save();
        }
      }
      /* else {
         //$entity = $this->entityManager->loadEntityByUuid('datatank_dataset', $field_storage_definitions[$field_name]->get('uuid'));

         //$entity->delete();
         //field_purge_batch(10);
       }*/
    }

    $this->configManager->set('enabled_dataset_fields', $values['choose_columns']);
    $this->configManager->save();
  }

}

?>