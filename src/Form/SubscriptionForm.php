<?php

/**
 * @file
 * Contains Drupal\datatank\Form\SubscriptionForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\flexmail\FlexmailHelper\FlexmailHelper;

/**
 * Class SubscriptionForm.
 *
 * @package Drupal\datatank\Form
 */
class SubscriptionForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'datatank_subscription_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
      $config = \Drupal::config('datatank.settings');

      $newsletter_intro = $config->get('newsletter_intro');
      $form['#prefix'] = check_markup($newsletter_intro['value'], $newsletter_intro['format']);

      $form['email'] = array(
        '#type' => 'email',
        '#title' => $this->t('Email address'),
        '#default_value' => '',
      );

      $form['actions'] = array(
        'submit' => array(
          '#type' => 'submit',
          '#value' => t('Subscribe'),
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
      $flexmail_config = \Drupal::config('flexmail.settings');
      $config = \Drupal::config('datatank.settings');

      $newsletter_success = $config->get('newsletter_success');
      $success_message = (string) check_markup($newsletter_success['value'], $newsletter_success['format']);
      $newsletter_error = $config->get('newsletter_error');
      $error_message = (string) check_markup($newsletter_error['value'], $newsletter_error['format']);
      $response = FlexmailHelper::subscribe(
        $form_state->getValue('email'),
        $flexmail_config->get('default_list_id'),
        \Drupal::languageManager()->getCurrentLanguage()->getId(),
        $success_message
      );

      if ($response instanceof \Exception) {
        if ($response->getCode() == 225) {
          drupal_set_message(t($success_message));
        }
        else {
          drupal_set_message(t($error_message, array('@message' => $response->getMessage())), 'error');
        }
      }
    }
}
