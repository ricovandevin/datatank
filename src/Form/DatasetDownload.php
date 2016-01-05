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
    // FILTER
    $form['filter'] = [
      '#type' => 'container',
    ];

    $form['filter']['title'] = [
      '#markup' => '<h2>' . $this->t('Filter data') . '</h2>',
    ];

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

    $regions = [];
    foreach ($regions_raw as $reg) {
      $regions[$reg->tid] = $reg->name;
    }

    $form['filter']['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Tourist region'),
      '#options' => $regions,
      '#states' => array(
        'visible' => array(
          ':input[name="location"]' => array('value' => 'region'),
        ),
      ),
    ];

    $postal_codes_raw = array_map('str_getcsv', file(drupal_get_path("module", 'datatank') . '/zipcodes2.csv'));
    $postal_codes = [];

    foreach ($postal_codes_raw as $postal_code_raw) {
      $postal_codes[strtolower($postal_code_raw[1])] = $postal_code_raw[1];
    }

    $form['filter']['town'] = [
      '#type' => 'select',
      '#title' => $this->t('Town'),
      '#options' => $postal_codes,
      '#states' => array(
        'visible' => array(
          ':input[name="location"]' => array('value' => 'town'),
        ),
      ),
    ];

    $form['filter']['coor'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#states' => array(
        'visible' => array(
          ':input[name="location"]' => array('value' => 'coor'),
        ),
      ),
    ];

    $config = \Drupal::configFactory()->getEditable('datatank.settings');
    $url = Url::fromUserInput($config->get('datatank_link_lambert72'));
    $external_link = \Drupal::l('?', $url);

    $form['filter']['coor']['info'] = [
      '#type' => 'markup',
      '#markup' => t('1) Lambert 72 coordinates') . $external_link,
    ];

    $form['filter']['coor']['x_coord'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X='),
      '#placeholder' => t('Ex: 188600'),
    ];

    $form['filter']['coor']['y_coord'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Y='),
      '#placeholder' => t('Ex: 188600'),
    ];

    $form['filter']['coor']['radiusinfo'] = [
      '#markup' => t('2) Radius in meters'),
    ];

    $form['filter']['coor']['radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Range (meters)'),
      '#placeholder' => t('Ex: 5000'),
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

    // laatste wijziging
    $form['filter']['timestamp'] = [
      '#type' => 'date',
      '#title' => $this->t('Last changed')
    ];

    $form['filter']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];


    $form['language'] = [
      '#type' => 'container'
    ];

    $form['language']['langcode'] = [
      '#title' => $this->t('Choose a language'),
      '#type' => 'select',
      '#options' => datatank_available_languages(),
    ];

    $form['language']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply')
    ];

    // RESULTAAT RECHTS
    $config = new DrupalConfig();
    $client = new Client($config);
    $query = ['limit' => -1];

    switch ($form_state->getValue('location')) {
      case 'town' :
        if ($form_state->hasValue('town')) {
          $query['city'] = $form_state->getValue('town');
        }
        break;

      case 'region':
        if ($form_state->hasValue('region')) {
          $term = \Drupal::entityManager()
            ->getStorage('taxonomy_term')
            ->load($form_state->getValue('region'));
          $query['region'] = $term->get('name')->value;
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
      '#type' => 'container'
    ];

    $form['result']['count'] = [
      '#markup' => $this->t('@count data found in dataset', array('@count' => count($result)))
    ];

    $form['result']['download'] = [
      '#markup' => $this->t('Download as:'),
    ];

    $formats = datatank_available_formats();
    foreach ($formats as $key => $format) {
      $data_url = Url::fromUri($config->getEndpoint() . $datatank_dataset->getName() . $format['extensie'], ['query' => $query]);
      $url = Url::fromRoute('datatank.dataset_download_confirm_index', [], ['query' => ['download_url' => $data_url->toString()]]);

      $form['result']['download'][$key] = [
        '#markup' => '<div>' . \Drupal::l($format['label'], $url) . '</div>'
      ];
    }


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

}
