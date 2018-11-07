<?php

namespace Drupal\cfr_wiki\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class WikiController extends ControllerBase {

  /**
   *
   */
  public function wiki_search($search_text) {
    $results_per_page = 10;
    $search_results = [];
    if ($search_text) {
      $client = \Drupal::httpClient();
      $page = \Drupal::request()->query->get('page');
      $offset = $results_per_page * $page;
      $url = 'https://en.wikipedia.org/w/api.php?action=query&list=search&utf8=&formatversion=2&prop=info&format=json&srwhat=text&inprop=url&srsearch=' . $search_text . '&sroffset=' . $offset;
      $request = $client->request('GET', $url);
      $results = json_decode($request->getBody());
      foreach ($results->query->search as $key => $search_result) {
        $url = 'https://en.wikipedia.org/w/api.php?action=query&prop=info&inprop=url&format=json&pageids=' . $search_result->pageid;
        $page_request = $client->get($url);
        $page_result = json_decode($page_request->getBody());
        $search_results[] = [
          '#theme' => 'wiki_search_result',
          '#title' => $search_result->title,
          '#summary' => $search_result->snippet,
          '#link' => $page_result->query->pages->{$search_result->pageid}->fullurl,
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
