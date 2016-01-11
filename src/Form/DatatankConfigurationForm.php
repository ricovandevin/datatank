<?php

/**
 * @file
 * Contains Drupal\datatank\Form\DatatankConfigurationForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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
  }

}
