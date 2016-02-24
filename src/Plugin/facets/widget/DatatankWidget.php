<?php

/**
 * @file
 * Contains \Drupal\facets\Plugin\facets\widget\LinksWidget.
 */

namespace Drupal\datatank\Plugin\facets\widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\facets\FacetInterface;
use Drupal\facets\Widget\WidgetInterface;

/**
 * The links widget.
 *
 * @FacetsWidget(
 *   id = "DatatankWidget",
 *   label = @Translation("List of links with fake checkboxes"),
 *   description = @Translation("A simple widget that shows a list of links with checkboxes in front"),
 * )
 */
class DatatankWidget implements WidgetInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute() {
    // Execute all the things.
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    /** @var \Drupal\facets\Result\Result[] $results */
    $results = $facet->getResults();
    $form = [];

    $configuration = $facet->getWidgetConfigs();
    $show_numbers = empty($configuration['show_numbers']) ? FALSE : (bool) $configuration['show_numbers'];

    foreach ($results as $result) {
      // Get the link.
      $text = $result->getDisplayValue();

      $url = $result->getUrl()->toString();
      $form[$url] = array(
        '#type' => 'checkbox',
        '#title' => '<a href="' . $url . '">' . $text . ' (' . $result->getCount() . ')</a>',
        '#attributes' => ['class' => ['facets-checkbox']],
      );

      if ($result->isActive()) {
        $form[$url]['#attributes']['checked'] = 'checked';
      }
    }

    $form['#attached']['library'][] = "datatank/checkboxWidget";

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, $config) {

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueryType($query_types) {
    return $query_types['string'];
  }

}
