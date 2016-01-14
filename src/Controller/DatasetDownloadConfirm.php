<?php

/**
 * @file
 * Contains \Drupal\datatank\Controller\DatasetDownloadConfirm.
 */

namespace Drupal\datatank\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Drupal\Core\Url;


/**
 * Class DatasetDownloadConfirm.
 *
 * @package Drupal\datatank\Controller
 */
class DatasetDownloadConfirm extends ControllerBase {

  /**
   * Download file.
   *
   */
  public function download() {
    $url = $_GET['download_url'];

    $pathinfo = strtok($url, '?');
    $pathinfo = pathinfo($pathinfo);

    $temp_name = drupal_tempnam(file_directory_temp(), 'file');

    // Download file to temp folder
    $client = new \GuzzleHttp\Client();
    $request = $client->get($url, ['save_to' => $temp_name]);

    // Send file to client
    $response = new BinaryFileResponse($temp_name);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      'dataset.' . $pathinfo['extension']
    );
    return $response;
  }

  public function index() {
    $url = $_GET['download_url'];
    $url = Url::fromRoute('datatank.dataset_download_confirm_dowload', [], ['query' => ['download_url' => $url]]);

    $markup = '<p>';
    $markup .= t('Depending on the chosen format, your data will be downloaded or will appear in your browser.');
    $markup .= '</br>';
    $markup .= t('This operation may take a while depending on the number of requested data.');
    $markup .= '</br>';
    $markup .= t('@link if your download isn\'t working.', ['@link' => \Drupal::l(t('Click here'), $url)]);
    $markup .= '</p>';
    $markup .= "<iframe width='1' height='1' frameborder='0' src='" . $url->toString() . "'></iframe>";

    return [
      '#markup' => $markup,
      '#allowed_tags' => ['iframe', 'a', 'br', 'p'],
    ];
  }

}
