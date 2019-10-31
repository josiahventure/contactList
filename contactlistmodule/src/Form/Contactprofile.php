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
use Drupal\cmrf_core\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Contactprofile extends FormBase {
public function getFormId() {
    return 'contact_profile';
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
   $call=$this->core->createCall($this->connector(),'Contact','get',array('return'=>'display_name, email, phone, id', 'limit' => 50,),array());
   $this->core->executeCall($call);
   return $call->getReply();
  }

  /**
  * form
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
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
      }
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
