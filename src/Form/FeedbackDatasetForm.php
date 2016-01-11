<?php

/**
 * @file
 * Contains Drupal\datatank\Form\FeedbackDatasetForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FeedbackDatasetForm.
 *
 * @package Drupal\datatank\Form
 */
class FeedbackDatasetForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'datatank_feedback_dataset_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
      $config = \Drupal::config('datatank.settings');

      $intro = $config->get('feedback_intro');
      $form['#prefix'] = check_markup($intro['value'], $intro['format']);

      $form['email'] = array(
        '#type' => 'email',
        '#title' => $this->t('Email address'),
        '#default_value' => '',
        '#weight' => -20,
      );

      $form['actions'] = array(
        'submit' => array(
          '#type' => 'submit',
          '#value' => t('Submit'),
          '#weight' => 20,
        ),
      );

      return $form;
    }

    /**
     * Implements \Drupal\Core\Form\FormInterface::validateForm().
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      if (!\Drupal::service('email.validator')->isValid($form_state->getValue('email'))) {
        $form_state->setErrorByName('email', t('Please provide a valid email address.'));
      }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      // Send feedback.
    }

}
