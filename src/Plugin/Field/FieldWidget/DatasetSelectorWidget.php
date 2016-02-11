<?php

/**
 * @file
 * Contains \Drupal\datatank\Plugin\Field\FieldWidget\DatasetSelectorWidget.
 */

namespace Drupal\datatank\Plugin\Field\FieldWidget;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Plugin implementation of the 'datatank_dataset_selector' widget.
 *
 * @FieldWidget(
 *   id = "datatank_dataset_selector",
 *   label = @Translation("Dataset selector"),
 *   field_types = {
 *     "string"
 *   },
 * )
 */
class DatasetSelectorWidget extends OptionsSelectWidget {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // A string type field does not have a setting for 'multiple'. We want to
    // have a single value select for this widget so let's force it.
    $element['#multiple'] = 0;

    // If we have a triggering element the form has been submitted and we are
    // rebuilding it in an Ajax call.
    if (!empty($form_state->getTriggeringElement())) {
      // We might want to expand this to a multi valued field later.
      $delta = 0;

      $field_feedback_dataset = $form_state->getValue('field_feedback_dataset');
      $target_tid = isset($field_feedback_dataset[$delta]['target']) ? $field_feedback_dataset[$delta]['target'] : '';
      $category_tid = isset($field_feedback_dataset[$delta]['category']) ? $field_feedback_dataset[$delta]['category'] : '';

      $element['#options'] = $this->getOptions($items->getEntity(), $target_tid, $category_tid);
    }

    // Add filter select for usability.
    $original_element = $element;
    $original_element['#title'] = $this->t('Dataset');
    $original_element['#title_display'] = 'before';
    $original_element['#weight'] = 10;
    $original_element['#description'] = $this->t('Use the target group and category dropdowns to limit the list of datasets.');
    $element = [
      '#type' => 'fieldset',
      'target' => $this->getTargetFormElement($form, $form_state),
      'category' => $this->getCategoryFormElement($form, $form_state),
      'dataset' => $original_element,
      '#prefix' => '<div id="dataset-wrapper">',
      '#suffix' => '</div>',
    ];

    // Force rebuilds because we are modifying the form in Ajax calls.
    $form_state->setCached(FALSE);
    $form_state->setRebuild();

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected function getOptions(FieldableEntityInterface $entity, $target_group = '', $category = '') {
    if (!isset($this->options) || $target_group || $category) {
      // Get all datasets.
      $query = \Drupal::entityQuery('datatank_dataset');

      if ($target_group) {
        $query->condition('field_dataset_target_group', $target_group);
      }

      if ($category) {
        $query->condition('field_dataset_category', $category);
      }

      $datasets = $query->execute();

      $options = ['' => '- ' . $this->t('Select a dataset') . ' -'];
      foreach ($datasets as $did) {
        $db = \Drupal::database();
        $dataset = $db->select('datatank_dataset_field_data', 'd')
          ->fields('d', ['name'])
          ->condition('did', $did)
          ->execute()
          ->fetchField();

        $options[$dataset] = $dataset;
      }

      $this->options = $options;
    }

    return $this->options;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Load selected options dynamically.
   */
  protected function getSelectedOptions(FieldItemListInterface $items, $delta = 0) {
    return [];
  }

  /**
   * @todo Write function documentation.
   */
  protected function getTargetFormElement(array $form, FormStateInterface $form_state) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $tids = $query
      ->condition('vid', 'field_dataset_target_group')
      ->execute();

    $options = ['' => $this->t('All target groups')];
    foreach ($tids as $tid) {
      $db = \Drupal::database();
      $target_group = $db->select('taxonomy_term_field_data', 't')
        ->fields('t', ['name'])
        ->condition('tid', $tid)
        ->execute()
        ->fetchField();

      $options[$tid] = $target_group;
    }

    $element = [
      '#type' => 'select',
      '#title' => $this->t('Target group'),
      '#required' => FALSE,
      '#options' => $options,
      '#weight' => -10,
      '#ajax' => array(
        'callback' => 'Drupal\datatank\Plugin\Field\FieldWidget\DatasetSelectorWidget::filterCategoryOptions',
        'method' => 'replace',
        'wrapper' => 'dataset-wrapper',
        'progress' => array(
          'message' => NULL,
        ),
      ),
    ];

    return $element;
  }

  /**
   * @todo Write function documentation.
   */
  protected function getCategoryFormElement(array $form, FormStateInterface $form_state) {
    // If we have a triggering element the form has been submitted and we are
    // rebuilding it in an Ajax call.
    if (!empty($form_state->getTriggeringElement())) {
      // We might want to expand this to a multi valued field later.
      $delta = 0;

      $field_feedback_dataset = $form_state->getValue('field_feedback_dataset');
      $target_tid = $field_feedback_dataset[$delta]['target'];

      $tids = $this->getAvailableCategoriesForTargetGroup($target_tid);
    }

    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', 'field_dataset_category');
    if (!empty($tids)) {
      $query->condition('tid', $tids, 'IN');
    }
    $tids = $query->execute();

    $options = ['' => $this->t('All categories')];
    foreach ($tids as $tid) {
      $db = \Drupal::database();
      $category = $db->select('taxonomy_term_field_data', 't')
        ->fields('t', ['name'])
        ->condition('tid', $tid)
        ->execute()
        ->fetchField();

      $options[$tid] = $category;
    }

    $element = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#required' => FALSE,
      '#options' => $options,
      '#weight' => 0,
      '#ajax' => array(
        'callback' => 'Drupal\datatank\Plugin\Field\FieldWidget\DatasetSelectorWidget::filterCategoryOptions',
        'method' => 'replace',
        'wrapper' => 'dataset-wrapper',
        'progress' => array(
          'message' => NULL,
        ),
      ),
    ];

    return $element;
  }

  /**
   * @todo Write function documentation.
   */
  public static function filterCategoryOptions(array $form, FormStateInterface $form_state) {
    // We might want to expand this to a multi valued field later.
    $delta = 0;

    return $form['field_feedback_dataset']['widget'][$delta];
  }

  /**
   * @todo Write function documentation.
   */
  private function getAvailableCategoriesForTargetGroup($tid) {
    // Load all datasets in target group $tid.
    $query = \Drupal::entityQuery('datatank_dataset');
    $datasets = $query
      ->condition('field_dataset_target_group', $tid)
      ->execute();

    // List all categories for these datasets.
    $category_tids = [];
    foreach ($datasets as $did) {
      $db = \Drupal::database();
      $category = $db->select('datatank_dataset__field_dataset_category', 'c')
        ->fields('c', ['field_dataset_category_target_id'])
        ->condition('bundle', 'datatank_dataset')
        ->condition('entity_id', $did)
        ->execute()
        ->fetchField();

      $category_tids[$category] = $category;
    }

    return $category_tids;
  }
}
