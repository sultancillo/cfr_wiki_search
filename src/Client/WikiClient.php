<?php

namespace Drupal\cfr_wiki\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Wikimedia search API Client Service.
 *
 * This class can issue search requests to wikimedia installs via their API.
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

  /**
   * Set the endpoint for the Wikimedia instance API.
   */
  public function setApiEndPoint($endpoint_uri) {
    $this->endpointUri = $endpoint_uri;
  }

  /**
   * Perform the search action and return the results.
   */
  public function search($search_text = '', $results_per_page = 10, $page = 0) {
    $results = [];
    if (!is_numeric($results_per_page)) {
      $results_per_page = 0;
    }
    if (!is_numeric($page)) {
      $page = 0;
    }
    if ($search_text != '' and $results_per_page > 0 and $page >= 0) {
      try {
        $offset = $results_per_page * $page;
        $url = $this->endpointUri;
        $query = [
          'query' => [
            'action' => 'query',
            'list' => 'search',
            'utf8' => '',
            'formatversion' => '2',
            'prop' => 'info',
            'format' => 'json',
            'srwhat' => 'text',
            'srlimit' => $results_per_page,
            'srsearch' => $search_text,
            'sroffset' => $offset,
          ],
        ];
        $request = $this->httpClient->request('GET', $url, $query);
        $results = json_decode($request->getBody());
        if (isset($results->query->search)) {
          foreach ($results->query->search as $key => $result) {
            $article_uri = $this->getPageUri($result->pageid);
            $results->query->search[$key]->uri = $article_uri;
          }
        }
      }
      catch (RequestException $exception) {
        drupal_set_message(t('Failed to complete Wikimedia API request "%error"', ['%error' => $exception->getMessage()]), 'error');
        \Drupal::logger('cfr_wiki')->error('Failed to complete Wikipedia API request "%error"', ['%error' => $exception->getMessage()]);
      }
    }
    return $results;
  }

  /**
   * Get the wikimedia page uri for a page id.
   */
  public function getPageUri($page_id) {
    $uri = '';
    if ($page_id != '') {
      try {
        $url = $this->endpointUri;
        $query = [
          'query' => [
            'action' => 'query',
            'prop' => 'info',
            'inprop' => 'url',
            'utf8' => '',
            'format' => 'json',
            'pageids' => $page_id,
          ],
        ];
        $request = $this->httpClient->request('GET', $url, $query);
        $results = json_decode($request->getBody());
        if (isset($results->query->pages->{$page_id}->fullurl)) {
          $uri = $results->query->pages->{$page_id}->fullurl;
        }
      }
      catch (RequestException $exception) {
        drupal_set_message(t('Failed to complete Wikimedia API request "%error"', ['%error' => $exception->getMessage()]), 'error');
        \Drupal::logger('cfr_wiki')->error('Failed to complete Wikipedia API request "%error"', ['%error' => $exception->getMessage()]);
      }
    }

    return $uri;
  }

}
