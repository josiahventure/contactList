<?php
  /**
   * @file
   * Contains \Drupal\contactlistmodule\Form\Contactprofile
   */
namespace Drupal\contactlistmodule\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides an RSVP Email form.
 */
class Contact extends FormBase {
public function getFormId() {
    return 'contacts';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
    ];
    // do not cache this page.
    $form['$cache']['max-age'] = 0;

    $form['message'] = array(
      '#markup' => $this->t('Nothing here'),
      '#prefix' => '<div id=contact_table>',
      '#suffix' => '</div>',
    );

    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message(t('The form is working.'));
  }

  public function processTable(&$element, FormStateInterface $form_state, &$complete_form) {


  }


}
