<?php
  /**
   * @file
   * Contains \Drupal\contactlist\Form\Contactlist
   */
namespace Drupal\contactlistmodule\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\cmrf_core\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Contactlist extends FormBase {
  public function getFormId() {
      return 'contact_list';
    }

  /**
  * get cmrf core
  */
  public static function create(ContainerInterface $container) {
   $core = $container->get('cmrf_core.core');
   return new static($core);
  }

  public function __construct(core $core) {
       $this->core = $core;
  }

  private function connector() {
   return \Drupal::config('cmrf_example.settings')->get('connector');
  }

  /**
  * connect to civicrm api3
  */
  public function getContactIds() {
   $call=$this->core->createCall($this->connector(),'Contact','get', ['return' => ['display_name, email, phone, id'], 'sort' =>  'display_name DESC ' , 'offset' => 10, 'limit' => 50],array());
   $this->core->executeCall($call);
   return $call->getReply();
}

  /**
  * form
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $header = array(
      'name' => array('data' => t('Name'), 'field' => 'c.display_name'),
      'email' => array('data' => t('Email'), 'field' => 'e.email'),
      'phone' => array('data' => t('Phone'), 'field' => 'p.phone'),
    );

    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
          $name = array('data' => $entry['display_name'], 'class' => 'link', 'id' => $entry['id']);
          $email = array('data' => $entry['email'], 'class' => 'link', 'id' => $entry['id']);
          $phone = array('data' => $entry['phone'], 'class' => 'link', 'id' => $entry['id']);
          $profile = array('data' => t(""), 'colspan' => '3');
          $loading = array('data' => t("LOADING"), 'colspan' => '3');
          $attributes = array('class' => array('content'));
          $profile_id = 'profile_' . $entry['id'];
          $loading_id = 'loading_' . $entry['id'];

          $rows[$entry['id']] = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            '#attributes' => $attributes
          );   // row with basic info
          $rows[$profile_id] = array(
            'name' => $profile,
            '#attributes' => array('class' => array('profile', $profile_id))
          );   // profile row, hidden in default
          $rows[$loading_id] = array(
            'name' => $loading,
            '#attributes' => array('class' => array('loading', $loading_id))
          );   // loading row hidden in default
        }
      }
    }

    $form['table'] = array(
      '#id'    => 'select-attendees',
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#empty' => t('No entries available.'),
    );
    // do not cache this page.
    $form['$cache']['max-age'] = 0;
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('submit'),
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
