<?php

namespace Drupal\cfr_wiki\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the WikiSearchForm form controller.
 *
 * A simple form with a singe text input element that submits a search
 * to the wikipedia english pages.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class WikiSearchForm extends FormBase {

  /**
   * Build the WikiSearch form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $default_search_text = '';
    $current_route = \Drupal::routeMatch();
    if ($current_route->getRouteName() == 'cfr_wiki.search') {
      $default_search_text = $current_route->getParameter('search_text');
    }
    $form['search_text'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $default_search_text,
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];
    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'cfr_wiki_search_form';
  }

  /**
   * Implements the Wikipedia search form submit handler.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $search_text = urlencode($form_state->getValue('search_text'));
    $form_state->setRedirect('cfr_wiki.search', ['search_text' => $search_text]);
  }

}
