<?php
namespace Drupal\contactlistmodule\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\cmrf_core\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Defines FirstController class.
 */
class ContactProfile extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
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
     $call=$this->core->createCall($this->connector(),'Contact','get',array('return'=>'display_name, email, phone, street_address, city, postal_code, id', 'id' => $id,),array());
     $this->core->executeCall($call);
     return $call->getReply();
   }

   function submit_callback(array &$form, FormStateInterface &$form_state) {
  drupal_set_message(t('Your form has been saved.'));
}



  public function content() {
    foreach ($entries = $this->getContactIds() as $entry2) {
      if (is_array($entry2) || $entry2 instanceof Traversable) {
        foreach ($entry2 as $entry) {
            $name = $entry['display_name'];
            $email = $entry['email'];
            $street_address = $entry['street_address'];
            $city = $entry['city'];
            $postal_code = $entry['postal_code'];
            $id = $entry['id'];
    }
    }
    }



    $form['basic_info_edit'] = \Drupal::formBuilder()->getForm('\Drupal\contactlistmodule\Form\Profile_basic_info_edit');
    $form['basic_info_edit']['#prefix']='<div class="profile_block">';
    $form['basic_info_edit']['#suffix']='</div>';
    $form['basic_info_edit']['var']=array(
      1 => array(
        'name' => t('name'),
        'val' => $name,
      ),
      2 => array(
        'name' => t('email'),
        'val' => $email,
      ),
    );
    $form['basic_info_edit']['id']=basic.$id;
    $form['basic_info_edit']['name']=t("Basic info");
    $form['basic_info_edit']['#theme']="contacts_theme";


    $form['basic_relationship_edit'] = \Drupal::formBuilder()->getForm('\Drupal\contactlistmodule\Form\Profile_relationship_edit');
    $form['basic_relationship_edit']['#prefix']='<div class="profile_block">';
    $form['basic_relationship_edit']['#suffix']='</div>';
    $form['basic_relationship_edit']['var']=array(
      1 => array(
        'name' => t('relationship'),
        'val' => "relationship",
      ),
    );
    $form['basic_relationship_edit']['id']=rel.$id;
    $form['basic_relationship_edit']['name']=t("Relationship");
    $form['basic_relationship_edit']['#theme']="contacts_theme";


    $form['basic_group_edit'] = \Drupal::formBuilder()->getForm('\Drupal\contactlistmodule\Form\Profile_group_edit');
    $form['basic_group_edit']['#prefix']='<div class="profile_block">';
    $form['basic_group_edit']['#suffix']='</div>';
    $form['basic_group_edit']['var']=array(
      1 => array(
        'name' => t('group'),
        'val' => "group",
      ),
    );
    $form['basic_group_edit']['id']=group.$id;
    $form['basic_group_edit']['name']=t("Group");
    $form['basic_group_edit']['#theme']="contacts_theme";




    $form['address_edit'] = \Drupal::formBuilder()->getForm('\Drupal\contactlistmodule\Form\Profile_address_edit');
    $form['address_edit']['#prefix']='<div class="profile_block">';
    $form['address_edit']['#suffix']='</div>';
    $form['address_edit']['var']=array(
      1 => array(
        'name' => t('street address'),
        'val' => $street_address,
      ),
      2 => array(
        'name' => t('city'),
        'val' => $city,
      ),
      3 => array(
        'name' => t('postal code'),
        'val' => $postal_code,
      ),
    );
    $form['address_edit']['id']=address.$id;
    $form['address_edit']['name']=t("Address");
    $form['address_edit']['#theme']="contacts_theme";




    // This is the important part, because will render only the TWIG template.
    return $form;
  }
}
