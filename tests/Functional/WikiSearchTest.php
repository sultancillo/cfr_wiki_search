<?php

namespace Drupal\Tests\cfr_wiki\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests for the cfr_wiki module.
 *
 * @ingroup cfr_wiki
 *
 * @group cfr_wiki
 */
class WikiSearchTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['cfr_wiki'];

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * Test Wiki Search routes.
   *
   * Test the following:
   * - That you can successfully access the wiki_search form.
   */
  public function testWikiSearchPage() {

    $assert = $this->assertSession();

    // Verify if we can successfully access the Wiki Search form.
    $this->drupalGet('wiki');
    $assert->statusCodeEquals(200);

    // Verify Search Form is present.
    $assert->elementExists('css', 'form[id="cfr-wiki-search-form"]');
    // Verify we have an intro element.
    $assert->elementExists('css', '.wiki-search-intro');
    // Search results should not be present.
    $assert->elementNotExists('css', '.wiki-search-results');
    $assert->elementNotExists('css', '.wiki-search-result');
    // Test form submission.
    $this->drupalPostForm(NULL, [
      'search_text' => 'Einstein',
    ], t('Search'));
    // Check that the search phrase is present..
    $this->assertText('Einstein');
    // Some search results should be present.
    $assert->elementExists('css', '.wiki-search-results');
    $assert->elementExists('css', '.wiki-search-result');

    // Verify if the can successfully access the Wiki Search form with a parameter.
    $this->drupalGet('wiki/Einstein');
    $assert->statusCodeEquals(200);

    // Verify Search Form is present.
    $assert->elementExists('css', 'form[id="cfr-wiki-search-form"]');
    // Verify we have an intro element.
    // Search results should be present.
    $assert->elementExists('css', '.wiki-search-results');
    $assert->elementExists('css', '.wiki-search-result');

    // Verify if the can successfully access the Wiki Search form
    // with a parameter with forward slashes.
    $this->drupalGet('wiki/Einstein/manifold');
    $assert->statusCodeEquals(200);

    // Verify Search Form is present.
    $assert->elementExists('css', 'form[id="cfr-wiki-search-form"]');
    // Search results should be present.
    $assert->elementExists('css', '.wiki-search-results');
    $assert->elementExists('css', '.wiki-search-result');
  }

}
