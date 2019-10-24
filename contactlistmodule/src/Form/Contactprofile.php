<?php
  /**
   * @file
   * Contains \Drupal\contactlistmodule\Form\Contactprofile
   */
namespace Drupal\contactlistmodule\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Symfony\Component\HttpFoundation\Response;
/**
 * Provides an RSVP Email form.
 */
class Contactprofile extends FormBase {
public function getFormId() {
    return 'contact_profile';
  }
  protected function load() {
    $id = \Drupal::request()->request->get('id');
    $select = Database::getConnection()->select('civicrm_contact', 'c');
    $select->join('civicrm_email', 'e', 'c.id = e.contact_id');
    $select->join('civicrm_phone', 'p', 'c.id = p.contact_id');
    $select->condition('c.id', $id);
    $select->addField('c', 'display_name' );
    $select->addField('e', 'email');
    $select->addField('p', 'phone');
    $select->addField('c', 'id');
    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $entries;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($entries = $this->load() as $entry) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#value' => $entry['display_name'],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#value' => $entry['email'],
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => t('Phone'),
      '#value' => $entry['phone'],
    ];
    // do not cache this page.
    $form['$cache']['max-age'] = 0;

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('submit'),
    );
  }

    $form['#prefix'] = '<td colspan="10">';
    $form['#suffix'] = '</td>';

    return new Response(render($form));
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message(t('The form is working.'));
  }

  public function processTable(&$element, FormStateInterface $form_state, &$complete_form) {


  }


}
