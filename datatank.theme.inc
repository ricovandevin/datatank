<?php

/**
 * @file
 * Theme hook and callbacks.
 */

/**
 * Implements hook_theme().
 */
function datatank_theme(){
  $theme = array();

  $theme['datatank_property_field'] = array(
    'variables' => array(
      'label' => '',
      'label_position' => 'above',
      'value' => '',
    ),
  );

  return $theme;
}

function theme_datatank_property_field($variables) {
  if (empty($variables['value'])) {
    return '';
  }

  $label_class = $variables['label_position'] == 'above' ? 'field-label-above' : 'field-label-' . $variables['label_position'];
  $output = '<div class="field property-field ' . $label_class . ' clearfix">';
  if (!empty($variables['label'])) {

    $output .= '<div class="field-label">Hallo' . $variables['label'] . ': </div>';
  }
  $output .= '<div class="field-items">';
  $items = is_array($variables['value']) ? $variables['value'] : array($variables['value']);
  foreach ($items as $key => $item) {
    $zebra = $key % 2 == 0 ? 'even' : 'odd';
    $output .= '<div class="field-item ' . $zebra . '">' . $item . '</div>';
  }
  $output .= '</div>';
  $output .= '</div>';
  return $output;
}