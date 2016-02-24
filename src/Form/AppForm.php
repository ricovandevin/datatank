<?php

/**
 * @file
 * Contains \Drupal\datatank\Form\AppForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Class AppForm.
 *
 * @package Drupal\datatank\Form
 */
class AppForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'app_form';
  }

  public function title() {
    return $this->t('Submit app');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['personal'] = [
      '#markup' => '<h2>' . $this->t('Personal information') . '</h2>'
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['organisation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organisation'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail'),
      '#required' => TRUE,
    ];

    $form['english'] = [
      '#markup' => '<h2>' . $this->t('English information') . '</h2>'
    ];

    $form['title_en'] = [
      '#type' => 'textfield',
      '#title' => $this->t('English title of the application.'),
      '#required' => TRUE,
    ];

    $form['description_en'] = [
      '#type' => 'textarea',
      '#title' => $this->t('English description of the application.'),
      '#required' => TRUE,
    ];

    $form['dutch'] = [
      '#markup' => '<h2>' . $this->t('Dutch information') . '</h2>'
    ];

    $form['title_nl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Dutch title of the application.'),
      '#required' => TRUE,
    ];

    $form['description_nl'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Dutch description of the application.'),
      '#required' => TRUE,
    ];

    $form['app'] = [
      '#markup' => '<h2>' . $this->t('Application details') . '</h2>'
    ];

    $types_raw = \Drupal::entityManager()
      ->getStorage('taxonomy_term')
      ->loadTree('type');
    $types = [];
    foreach ($types_raw as $type) {
      $types[$type->tid] = $type->name;
    }

    $form['type'] = [
      '#type' => 'checkboxes',
      '#options' => $types,
      '#title' => $this->t('Type'),
      '#required' => TRUE,
    ];

    $form['image'] = [
      '#title' => $this->t('Image'),
      '#type' => 'managed_file',
      '#upload_location' => 'public://generated/images/app/',
      '#upload_validators' => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
        'file_validate_size' => array(file_upload_max_size()),
      ),
      '#description' => t('Allowed types: @extensions.', array('@extensions' => 'gif png jpg jpeg')) . '<br/>' . t('@size limit.', array('@size' => format_size(file_upload_max_size()))),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = Node::create(array(
        'type' => 'app',
        'title' => $form_state->getValue('title_en'),
        'langcode' => 'en',
        'uid' => '1',
        'status' => 0,
        'body' => $form_state->getValue('description_en'),
        'field_app_email' => $form_state->getValue('email'),
        'field_app_image' => $form_state->getValue('image'),
        'field_app_name' => $form_state->getValue('name'),
        'field_app_organisation' => $form_state->getValue('organisation'),
        'field_app_type' => $form_state->getValue('type'),
    ));
    $node->save();

    $values = [
      'title' => $form_state->getValue('title_nl'),
      'body' => $form_state->getValue('description_nl'),
      'uid' => '1',
    ];
    $translated_entity = $node->addTranslation('nl', $values);
    $translated_entity->save();
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

}
