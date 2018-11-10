<?php

namespace Drupal\cfr_wiki\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 *
 */
class WikiClient {
  protected $httpClient;

  protected $endpointUri;

  /**
   * Constructor.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function setApiEndPoint($endpoint_uri) {
    $this->endpointUri = $endpoint_uri;
  }

  /**
   * { @inheritdoc }.
   */
  public function search($search_text, $results_per_page, $page) {
    try {
      $offset = $results_per_page * $page;
      $url = $this->endpointUri . '?action=query&list=search&utf8=&formatversion=2&prop=info&format=json&srwhat=text&inprop=url&srsearch=' . $search_text . '&sroffset=' . $offset;
      $request = $this->httpClient->request('GET', $url);
      $results = json_decode($request->getBody());
    }
    catch (RequestException $exception) {
      drupal_set_message(t('Failed to complete Wikimedia API request "%error"', ['%error' => $exception->getMessage()]), 'error');
      \Drupal::logger('cfr_wiki')->error('Failed to complete Wikipedia API request "%error"', ['%error' => $exception->getMessage()]);
      return FALSE;
    }
    return $results;
  }

  public function get_page_uri($page_id) {
    try {
      $url = $this->endpointUri . '?action=query&prop=info&inprop=url&format=json&pageids=' . $page_id;
      $request = $this->httpClient->request('GET', $url);
      $results = json_decode($request->getBody());
    }
    catch (RequestException $exception) {
      drupal_set_message(t('Failed to complete Wikimedia API request "%error"', ['%error' => $exception->getMessage()]), 'error');
      \Drupal::logger('cfr_wiki')->error('Failed to complete Wikipedia API request "%error"', ['%error' => $exception->getMessage()]);
      return FALSE;
    }
    return $results->query->pages->{$page_id}->fullurl;
  }

}
