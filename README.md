crf_wiki
========

This Drupal 8 module implements the CFR coding challenge according to the following specs:

Create a Drupal 8 module that does the following:

1) Loads a page at /wiki which explains what this page does.
2) The page should include a 'Search' form field.
3) A user can either enter a value in the form field or provide a url parameter (/wiki/[parameter]).
4) If a URL parameter is provided then the page displays wikipedia articles containing the parameter in the title.
5) If no parameter is provided, then the page displays wikipedia articles for the term provided in the 'Search' form field.
6) The page should display the term that is being searched.
7) Search results should include the Title, a link to the article, and the extract for the article.
8) Your module should include functional tests and relevant documentation.
9) Check the module into github.

See: https://gist.github.com/mcewand/69510cebb214184174b5cf30b5b8298e

Requirements:
=============
A working Drupal 8 installation.

Installation instructions:
==========================
- Drop module directory into the modules/custom directory in Drupal.
- Activate the module.

Known Issues:
=============
Wikipedia has disabled title searching on their wikimedia install, so a full text search is being performed instead, including titles.
