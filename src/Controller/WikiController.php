<?php

namespace Drupal\cfr_wiki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cfr_wiki\Client\WikiClient;

/**
 *
 */
class WikiController extends ControllerBase {

  protected $wikiClient;

  /**
   *
   */
  public function __construct(WikiClient $client) {
    $client->setApiEndpoint('https://en.wikipedia.org/w/api.php');
    $this->wikiClient = $client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cfr_wiki.client')
    );
  }

  /**
   *
   */
  public function wiki_search($search_text) {
    $results_per_page = 10;
    $page = \Drupal::request()->query->get('page');
    if (!is_numeric($page)) {
      $page = 0;
    }
    else {
      $page = floor($page);
    }
    $client = \Drupal::httpClient();
    $search_results = [];
    if ($search_text) {
      $results = $this->wikiClient->search($search_text, $results_per_page, $page);
      foreach ($results->query->search as $key => $search_result) {
        $url = 'https://en.wikipedia.org/w/api.php?action=query&prop=info&inprop=url&format=json&pageids=' . $search_result->pageid;
        $page_request = $client->get($url);
        $page_result = json_decode($page_request->getBody());
        $page_url = $this->wikiClient->get_page_uri($search_result->pageid);
        $search_results[] = [
          '#theme' => 'wiki_search_result',
          '#title' => $search_result->title,
          '#summary' => $search_result->snippet,
          '#link' => $page_url,
        ];
      }
    }
    $search_form = $this->formBuilder()->getForm('Drupal\cfr_wiki\Form\WikiSearchForm');
    $total_hits = $results->query->searchinfo->totalhits;
    $pages = ceil($total_hits / $results_per_page);
    pager_default_initialize($total_hits, $results_per_page);
    $content['results'] = [
      '#theme' => 'wiki_search_results',
      '#results' => $search_results,
      '#search_form' => $search_form,
      '#search_text' => $search_text,
    ];
    $content['pager'] = [
      '#type' => 'pager',
    ];
    return $content;
  }

}
