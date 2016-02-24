<?php

/**
 * @file
 * Contains \Drupal\datatank\Form\DatasetDownload.
 */

namespace Drupal\datatank\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tdt_client\Client;
use Drupal\tdt_client\Config\DrupalConfig;
use Drupal\Core\Url;

/**
 * Class DatasetDownload.
 *
 * @package Drupal\datatank\Form
 */
class DatasetDownload extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dataset_download';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $datatank_dataset = NULL) {
    // Dataset intro
    $datatank_dataset->__set('download', TRUE);
    $entity_view = entity_view($datatank_dataset, 'full');
    $form['intro_dataset'] = [
      '#markup' => render($entity_view)
    ];


    // FILTER
    $form['filter'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['dataset-download__filters']
      ]
    ];

    $form['filter']['title'] = [
      '#markup' => '<h2>' . $this->t('Filter data') . '</h2>',
    ];

    if ($datatank_dataset->canBeFiltered()) {
      $form['filter']['location'] = [
        '#type' => 'radios',
        '#title' => $this->t('Location'),
        '#options' => [
          'no' => $this->t('Not filtered'),
          'region' => $this->t('Tourist region'),
          'town' => $this->t('Town'),
          'coor' => $this->t('At coordinates (Lambert 72)'),
        ],
        '#default_value' => 'no'
      ];

      $regions_raw = \Drupal::entityManager()
        ->getStorage('taxonomy_term')
        ->loadTree('region');

      // Needed for Select 2 empty option
      $regions = ['' => '', 0 => t('All regions')];

      foreach ($regions_raw as $reg) {
        $regions[$reg->tid] = $reg->name;
      }

      $form['filter']['region'] = [
        '#type' => 'select',
        '#title' => $this->t('Tourist region'),
        '#options' => $regions,
        '#default_value' => 0,
        '#states' => array(
          'visible' => array(
            ':input[name="location"]' => array('value' => 'region'),
          ),
        ),
        '#attributes' => [
          'data-placeholder' => t('All regions')
        ],
      ];

      $postal_codes_raw = array_map('str_getcsv', file(\Drupal::root() . '/' . drupal_get_path("module", 'datatank') . '/zipcodes.csv'));
      // Needed for Select 2 empty option
      $postal_codes = ['' => '', 0 => t('All towns')];

      foreach ($postal_codes_raw as $postal_code_raw) {
        //$postal_codes[strtolower($postal_code_raw[1])] = $postal_code_raw[0] . '-' . $postal_code_raw[1];
        $postal_codes[strtolower($postal_code_raw[1])] = $postal_code_raw[1];
      }

      $form['filter']['town'] = [
        '#type' => 'select',
        '#title' => $this->t('Town'),
        '#options' => $postal_codes,
        '#default_value' => 0,
        '#states' => array(
          'visible' => array(
            ':input[name="location"]' => array('value' => 'town'),
          ),
        ),
        '#attributes' => [
          'data-placeholder' => t('All towns')
        ],
      ];

      $form['filter']['coor'] = [
        '#type' => 'container',
        '#tree' => TRUE,
        '#states' => array(
          'visible' => array(
            ':input[name="location"]' => array('value' => 'coor'),
          ),
        ),
        '#attributes' => [
          'class' => ['dataset-download__filters-coordinates']
        ],
      ];

      $config = \Drupal::configFactory()->getEditable('datatank.settings');
      $url = Url::fromUserInput($config->get('datatank_link_lambert72'));
      $external_link = '<span class="info-link">' . \Drupal::l('?', $url) . '</span>';

      $form['filter']['coor']['info'] = [
        '#type' => 'markup',
        '#markup' => '<div class="dataset-download__filters-coordinates_label"><b>1. </b>' . t('Lambert 72 coordinates') . '</div>' . $external_link,
      ];

      $form['filter']['coor']['x_coord'] = [
        '#type' => 'textfield',
        '#title' => $this->t('X='),
        '#placeholder' => t('Ex. 188600'),
      ];

      $form['filter']['coor']['y_coord'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Y='),
        '#placeholder' => t('Ex. 188600'),
      ];

      $form['filter']['coor']['radiusinfo'] = [
        '#markup' => '<div class="dataset-download__filters-radius_label"><b>2. </b>' . t('Radius (in meters)') . '</div>',
      ];

      $form['filter']['coor']['radius'] = [
        '#type' => 'textfield',
        '#placeholder' => t('Ex. 5000'),
      ];

      $form['filter']['labels'] = [
        '#type' => "checkboxes",
        '#title' => $this->t('Labels'),
        '#options' => [
          'bicycle_friendly' => $this->t('Bicycle friendly'),
          'green_key' => $this->t('Green'),
          'accessibility' => $this->t('Accessibility'),
        ]
      ];
    }

    // Language
    $form['filter']['langcode'] = [
      '#title' => $this->t('Choose a language'),
      '#type' => 'select',
      '#options' => datatank_available_languages(),
    ];

    // laatste wijziging
    $form['filter']['timestamp'] = [
      '#type' => 'date',
      '#title' => $this->t('Last changed'),
      '#description' => $this->t('Format: @date', array('@date' => format_date(time(), 'custom', 'Y-m-d'))),
      //'#default_value' => array('year' => 2010, 'month' => 2, 'day' => 12),
      '#default_value' => '',
    ];

    $form['filter']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];


    /* $form['language'] = [
      '#type' => 'container',
      '#attributes' => [
      'class' => ['dataset-download__language']
      ]
      ];

      $form['language']['langcode'] = [
      '#title' => $this->t('Choose a language'),
      '#type' => 'select',
      '#options' => datatank_available_languages(),
      ];

      $form['language']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply')
      ]; */

    // RESULTAAT RECHTS
    $config = new DrupalConfig();
    $client = new Client($config);
    $query = ['limit' => -1];

    switch ($form_state->getValue('location')) {
      case 'town' :
        if ($form_state->hasValue('town') && $form_state->getValue('town')) {
          $query['city'] = $form_state->getValue('town');
        }
        break;

      case 'region':
        if ($form_state->hasValue('region') && $form_state->getValue('region')) {
          $term = \Drupal::entityManager()
            ->getStorage('taxonomy_term')
            ->load($form_state->getValue('region'));
          $query['region'] = $term->get('field_region_id')->value;
        }
        break;

      case "coor" :
        foreach ($form_state->getValue('coor') as $key => $val) {
          $query[$key] = $val;
        }
        break;
    }

    // Last changed
    if ($form_state->hasValue('timestamp')) {
      $query['timestamp'] = strtolower($form_state->getValue('timestamp'));
    }

    // Labels
    if ($form_state->hasValue('labels')) {
      foreach ($form_state->getValue('labels') as $key => $val) {
        if ($val) {
          $query[$key] = 1;
        }
      }
    }

    $query['lang'] = $form_state->getValue('langcode');
    // Result
    $data_url = Url::fromUri($config->getEndpoint() . $datatank_dataset->getName() . '.json', ['query' => $query]);
    $result = $client->get($data_url->toString());

    $form['result'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['dataset-download__result']
      ]
    ];

    $form['result']['count'] = [
      '#markup' => '<div class="dataset__results"><span class="dataset__results-amount">' . $this->t('@count ', array('@count' => count($result))) . '</span>' . t('data found in dataset') . '</div>',
    ];

    $form['result']['download'] = [
      '#markup' => '<div class="dataset-download__result-label">' . $this->t('Download as:') . '</div>',
    ];

    $formats = datatank_available_formats();
    foreach ($formats as $key => $format) {
      $data_url = Url::fromUri($config->getEndpoint() . $datatank_dataset->getName() . $format['extensie'], ['query' => $query]);
      $url = Url::fromRoute('datatank.dataset_download_confirm_index', [], ['query' => ['download_url' => $data_url->toString()]]);

      $form['result']['download'][$key] = [
        '#markup' => '<div class="button__download">' . \Drupal::l($format['label'], $url) . '</div>'
      ];
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasValue('timestamp') && $form_state->getValue('timestamp') != '') {
      $timestamp = $form_state->getValue('timestamp');
      if (strtotime($timestamp)) {
        $test_date = format_date(strtotime($timestamp), 'custom', 'Y-m-d');
        if ($test_date != $timestamp) {
          $form_state->setErrorByName('timestamp', $this->t('Last changed is not in the right format.'));
        }
      } else {
        $form_state->setErrorByName('timestamp', $this->t('Last changed is not in the right format.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function title($datatank_dataset) {
    return $datatank_dataset->field_dataset_title->value;
  }

}
