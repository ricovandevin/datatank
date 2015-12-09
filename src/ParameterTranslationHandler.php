<?php

/**
 * @file
 * Contains \Drupal\datatank\ParameterTranslationHandler.
 */

namespace Drupal\datatank;

use Drupal\Core\Entity\EntityInterface;
use Drupal\content_translation\ContentTranslationHandler;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the translation handler for Datatank parameter.
 */
class ParameterTranslationHandler extends ContentTranslationHandler {
  /**
   * Form submission handler for ParameterTranslationHandler::entityFormAlter().
   *
   * This handles the save action.
   *
   * @see \Drupal\Core\Entity\EntityForm::build().
   */
  public function entityFormSave(array $form, FormStateInterface $form_state) {
    if ($this->getSourceLangcode($form_state)) {
      $entity = $form_state->getFormObject()->getEntity();
      $form_state->setRedirectUrl($entity->urlInfo());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function entityFormAlter(array &$form, FormStateInterface $form_state, EntityInterface $entity) {
    parent::entityFormAlter($form, $form_state, $entity);
    $form['actions']['submit']['#submit'][] = array($this, 'entityFormSave');
  }

}

?>