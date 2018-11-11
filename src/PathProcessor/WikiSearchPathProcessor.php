<?php

namespace Drupal\cfr_wiki\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Implement InboundPathProcessorInterface.
 *
 * This allows slashes on incoming parameters on Wikipedia search pages.
 */
class WikiSearchPathProcessor implements InboundPathProcessorInterface {

  /**
   * Process inbound path and urlencode the parameter.
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
