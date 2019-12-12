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
use Drupal\Core\Routing\TrustedRedirectResponse;

class Profile_group_edit extends FormBase {
public function getFormId() {
    return 'contact_group_info';
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
    $id = \Drupal::request()->query->get('id');
    $call=$this->core->createCall($this->connector(),'Contact','get',array('return'=>'display_name, email, id', 'id' => $id,),array());
    $this->core->executeCall($call);
    return $call->getReply();
  }

  public function updateContact($name, $email) {
    $id = \Drupal::request()->query->get('id');
    $replace = ['values' => ['0' => ['is_primary' => 1, 'email' => $email, 'location_typ_id' => "Home"]]];
    $call=$this->core->createCall($this->connector(),'Contact','create',array('id' => $id, 'display_name' => $name, 'api.Email.replace' => $replace),array());
    $this->core->executeCall($call);
    drupal_set_message(t('Your form has been saved. Success'.$id));
  }


  /**
  * form
  */
  public function buildForm(array $form, FormStateInterface $form_state, $id = 1) {
    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
          $form['display_name'] = [
            '#type' => 'textfield',
            '#title' => t('Name'),
            '#default_value' => $entry['display_name'],
          ];
          $form['email'] = [
            '#type' => 'email',
            '#title' => t('Email'),
            '#default_value' => $entry['email'],
          ];

          $form['submit'] = array(
            '#type' => 'submit',
            '#default_value' => t('submit'),
          );
        }
      }
    }

    $form['#prefix'] = '<td colspan="10">';
    $form['#suffix'] = '</td>';

    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue($form['display_name']['#parents']);
    $email = $form_state->getValue($form['email']['#parents']);

    $this->updateContact($name, $email);

    drupal_set_message(t($name));
    $response = new TrustedRedirectResponse('http://drupal-new.dd:8083/contact2');
    $form_state->setResponse($response);
  }

  public function processTable(&$element, FormStateInterface $form_state, &$complete_form) {
  }
}
