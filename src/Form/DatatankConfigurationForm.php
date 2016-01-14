<?php

/**
 * @file
 * Contains Drupal\datatank\Form\DatatankConfigurationForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;

/**
 * Class DatatankConfigurationForm.
 *
 * @package Drupal\datatank\Form
 */
class DatatankConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'datatank.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'datatank_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('datatank.settings');

    $form['datatank_link_lambert72'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link to lambert 72 info page'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('datatank_link_lambert72'),
    );

    $form['datatank_link_info_filters'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link to info filters info page'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('datatank_link_info_filters'),
    );

    $newsletter_intro = $config->get('newsletter_intro');
    $form['newsletter_intro'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Introduction text for newsletter subscription page'),
      '#format' => isset($newsletter_intro['format']) ? $newsletter_intro['format'] : 'html',
      '#default_value' => isset($newsletter_intro['value']) ? $newsletter_intro['value'] : '',
    );

    $newsletter_success = $config->get('newsletter_success');
    $form['newsletter_success'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Success message for newsletter subscription'),
      '#format' => isset($newsletter_success['format']) ? $newsletter_success['format'] : 'html',
      '#default_value' => isset($newsletter_success['value']) ? $newsletter_success['value'] : '',
    );

    $newsletter_error = $config->get('newsletter_error');
    $form['newsletter_error'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Error message for newsletter subscription'),
      '#format' => isset($newsletter_error['format']) ? $newsletter_error['format'] : 'html',
      '#default_value' => isset($newsletter_error['value']) ? $newsletter_error['value'] : '',
    );

    $feedback_intro = $config->get('feedback_intro');
    $form['feedback_intro'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Introduction text for the feedback page'),
      '#format' => isset($feedback_intro['format']) ? $feedback_intro['format'] : 'html',
      '#default_value' => isset($feedback_intro['value']) ? $feedback_intro['value'] : '',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('datatank_link_lambert72', $form_state->getValue('datatank_link_lambert72'))
      ->save();

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('datatank_link_info_filters', $form_state->getValue('datatank_link_info_filters'))
      ->save();

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('newsletter_intro', $form_state->getValue('newsletter_intro'))
      ->save();

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('newsletter_success', $form_state->getValue('newsletter_success'))
      ->save();

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('newsletter_error', $form_state->getValue('newsletter_error'))
      ->save();

    \Drupal::configFactory()->getEditable('datatank.settings')
      ->set('feedback_intro', $form_state->getValue('feedback_intro'))
      ->save();


    Cache::invalidateTags(array('block_view'));
  }

}
