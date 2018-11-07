<?php

namespace Drupal\cfr_wiki\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Implement InboundPathProcessorInterface to allow slashes on incoming parameters.
 */
class WikiSearchPathProcessor implements InboundPathProcessorInterface {

  /**
   * Process inbound path and substitute forward slashes with colon for wiki search.
   */
  public function processInbound($path, Request $request) {
    if (strpos($path, '/wiki/') === 0) {
      $search_text = preg_replace('|^\/wiki\/|', '', $path);
      $search_text = urlencode($search_text);
      return "/wiki/$search_text";
    }
    return $path;
  }

}
