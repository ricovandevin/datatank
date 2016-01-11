<?php

/**
 * @file
 * Contains Drupal\datatank\Form\FeedbackDatasetUseForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\flexmail\FlexmailHelper\FlexmailHelper;

/**
 * Class FeedbackDatasetUseForm.
 *
 * @package Drupal\datatank\Form
 */
class FeedbackDatasetUseForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'datatank_feedback_dataset_use_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
      $form['#prefix'] = t('We would like to know which datasets you use so that we can make them even better.');

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
