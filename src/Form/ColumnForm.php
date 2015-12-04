<?php

/**
 * @file
 * Contains Drupal\datatank\Form\ColumnForm.
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
class ColumnForm extends ContentEntityForm {

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        /* @var $entity \Drupal\datatank\Entity\Column */
        $form = parent::buildForm($form, $form_state);
        $entity = $this->entity;

        $form['langcode'] = array(
            '#title' => $this->t('Language'),
            '#type' => 'language_select',
            '#default_value' => $entity->getUntranslated()->language()->getId(),
            '#languages' => Language::STATE_ALL,
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state) {
        $form_state->setRedirect('entity.datatank_column.collection');
        $entity = $this->getEntity();
        $entity->save();
    }
}

?>