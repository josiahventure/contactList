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
       $this->name = \Drupal::request()->request->get('name');
       $this->sort = \Drupal::request()->request->get('sort');
       $this->rows = \Drupal::request()->request->get('rows');
       $this->offset = \Drupal::request()->request->get('offset');
       if(!isset($this->name)){
         $this->name="";
       }

       if(!isset($this->sort)){
         $this->sort="display_name ASC";
       }
       if(!isset($this->rows)){
         $this->rows=50;
       }
       if(!isset($this->offset)){
         $this->offset=0;
         $this->pager=1;
       } else {
         $this->pager=$this->offset/$this->rows+1;
       }
  }

  private function connector() {
   return \Drupal::config('cmrf_example.settings')->get('connector');
  }

  /**
  * connect to civicrm api3
  */
  public function getContactIds() {
   $call=$this->core->createCall($this->connector(),'Contact','get', ['return' => 'display_name, email, phone, id', 'display_name' => $this->name],['sort' =>  $this->sort, 'limit' => $this->rows]);
   $this->core->executeCall($call);
   return $call->getReply();
}

public function getNumberOfContacts() {
  $call=$this->core->createCall($this->connector(),'Contact','get',array('return'=>'id'),array('limit' => 999999));
  $this->core->executeCall($call);
  $count = 0;
  foreach($call->getReply() as $pom) {
    foreach($pom as $contact) {
      $count++;
    }
  }
  return $count;
}

  /**
  * form
  */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['mytable'] = array(
      '#type' => 'table',
      '#title' => 'List of Nodes',
      '#header' => ['checkbox' => array('data' => t('<input type="checkbox" name="checkAll">')),
                    'name' => array('data' => t('Name'), 'class' => 'sortable', 'id' => 'nameSort'),
                    'email' => array('data' => t('Email'), 'class' => 'sortable', 'id' => 'emailSort'),
                    'Phone' => array('data' => t('Phone'), 'class' => 'sortable', 'id' => 'phoneSort')],
    );

    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
          $label=array(
            '#type' => 'label',
            '#title' => t($entry['display_name']),
            '#wrapper_attributes' => [
              'class' => ['link'],
              'id' => $entry['id']
            ],
            '#attributes' => [
              'class' => ['link'],
              'id' => $entry['id']
            ],
          );

          /**
          * normal row with contact
          */
          $form['mytable'][]=array(
            '#attributes' => ['class' => 'content',],
            '' => array(
              '#type' => 'checkbox',
              '#return_value' => $entry['id'],
            ),
            'Name' => array(
              '#type' => 'label',
              '#title' => t($entry['display_name']),
              '#wrapper_attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
              '#attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
            ),
            'Email' => array(
              '#type' => 'label',
              '#title' => t($entry['email']),
              '#wrapper_attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
              '#attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
            ),
            'Phone' => array(
              '#type' => 'label',
              '#title' => t($entry['phone']),
              '#wrapper_attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
              '#attributes' => [
                'class' => ['link'],
                'id' => $entry['id']
              ],
            )
          );

          /**
          * profile hidden in default
          */
          $form['mytable'][]=array('#attributes' => [
            'class' => ['profile','profile_'.$entry['id']]],
            'Profile' => array(
              '#type' => 'label',
              '#title' => t(''),
            ),
          );

          /**
          * loading hidden in default
          */
          $form['mytable'][]=array('#attributes' => [
            'class' => ['loading','loading_'.$entry['id']]],
            'Loading' => array(
              '#type' => 'label',
              '#title' => t('LOADING'),
              '#wrapper_attributes' => ['colspan' => '4'],
            ),
          );
        }
      }
    }
    $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t("submit"),
        );

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message(t('Your form has been saved. val'));
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $count=$this->getNumberOfContacts();
    drupal_set_message($count);

  }

  public function processTable(&$element, FormStateInterface $form_state, &$complete_form) {
  }
}
