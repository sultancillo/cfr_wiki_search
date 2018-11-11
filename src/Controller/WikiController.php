<?php

namespace Drupal\cfr_wiki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cfr_wiki\Client\WikiClient;

/**
 * Wikipedia search page controller.
 */
class WikiController extends ControllerBase {

  protected $wikiClient;

  /**
   * Dependency injection through the constructor.
   *
   * @param Drupal\cfr_wiki\Client\WikiClient $client
   *   Wikimedia Search Client Service.
   */
  public function __construct(WikiClient $client) {
    $client->setApiEndpoint('https://en.wikipedia.org/w/api.php');
    $this->wikiClient = $client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cfr_wiki.client')
    );
  }

  /**
   * Build the search page as a render array.
   *
   * @param string $search_text
   *   The text to search.
   */
  public function wikiSearch($search_text) {
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
      if (!empty($results)) {
        foreach ($results->query->search as $key => $search_result) {
          $search_results[] = [
            '#theme' => 'wiki_search_result',
            '#title' => $search_result->title,
            '#summary' => $search_result->snippet,
            '#link' => $search_result->uri,
          ];
        }
        $total_hits = $results->query->searchinfo->totalhits;
        $pages = ceil($total_hits / $results_per_page);
        pager_default_initialize($total_hits, $results_per_page);
      }
    }
    $search_form = $this->formBuilder()->getForm('Drupal\cfr_wiki\Form\WikiSearchForm');
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
