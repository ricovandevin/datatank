<?php

/**
 * @file
 * Contains Drupal\datatank\Form\ParameterForm.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the datatank_column entity edit forms.
 *
 * @ingroup datatank
 */
class ParameterForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    dsm($form['langcode']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.datatank_parameter.collection');
    $entity = $this->getEntity();
    $entity->save();
  }
}

?>