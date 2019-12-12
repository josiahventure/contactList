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
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class Profile_address_edit extends FormBase {
public function getFormId() {
    return 'contact_address';
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
    $call=$this->core->createCall($this->connector(),'Contact','get',array('return'=>'street_address, display_name, city, postal_code, id', 'id' => $id,),array());
    $this->core->executeCall($call);
    return $call->getReply();
  }

  public function updateContact($street, $city, $postal_code) {
    $id = \Drupal::request()->query->get('id');
    $replace = ['values' => ['0' => [ 'street_address' => $street, 'is_primary' => 1, 'city' => $city, 'postal_code' => $postal_code]]];
    $call=$this->core->createCall($this->connector(),'Contact','create',array('id' => $id, 'api.Address.replace' => $replace),array());
    $this->core->executeCall($call);
    drupal_set_message(t('Your form has been saved. Success'.$id));
  }

  /**
  * form
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
          $form['street'] = [
            '#type' => 'textfield',
            '#title' => t('Street Address'),
            '#default_value' => $entry['street_address'],
          ];
          $form['city'] = [
            '#type' => 'textfield',
            '#title' => t('City'),
            '#default_value' => $entry['city'],
          ];
          $form['postal'] = [
            '#type' => 'textfield',
            '#title' => t('Postal code'),
            '#default_value' => $entry['postal_code'],
          ];
        }
      }
    }
    
    $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t("submit"),
        );


    return $form;
  }

  public function validate($form, &$form_state){
    drupal_set_message('Validated');
}

    public function submitForm(array &$form, FormStateInterface $form_state) {
          $element = $form['street'];
          $street = $form_state->getValue($form['street']['#parents']);
          $city = $form_state->getValue($form['city']['#parents']);
          $postal_code = $form_state->getValue($form['postal']['#parents']);

          $this->updateContact($street, $city, $postal_code);

          drupal_set_message(t($street));
          $response = new TrustedRedirectResponse('http://drupal-new.dd:8083/contact2');
          $form_state->setResponse($response);
      }
}
