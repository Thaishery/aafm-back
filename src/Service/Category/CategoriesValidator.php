<?php
namespace App\Service\Category;

use App\Service\DefaultValidator;

class CategoriesValidator extends DefaultValidator {
  private $valiableModulesType;
  public function __construct()
  {
    parent::__construct();
    //? for now ... may be moved to it's own class to failitate maintnability . 
    $this->valiableModulesType = $this->setValiableModulesType();
    // $this->maxPost = 5;
  }

  public function validateCategory(object $postData){
    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;

    foreach($postData as $key =>$val){
      switch ($key){
        case 'name' :
          if(!preg_match('/[\w,{2,255}]/',$val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Should be any acsi char from 2 to 255 char';
          }
          break;
        case 'content' : 
          //? pas de titre : 
          if(!isset($val->title)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'titre requis.';
            break;
          }
          //? titre invalide : 
          if(!preg_match('/[\w,{2,255}]/',$val->title)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'titre non valide.';
            break;
          }
          if(!isset($val->modules)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'modules requis.';
            break;
          }
          if(!is_array($val->modules)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'les modules devrais être un tableau.';
            break;
          }
          $this->validateModules($val->modules);
          break;
      }
    }
    return $this->result;
  }
  private function validateModules($modules){
    foreach($modules as $key=>$val){
      foreach($val as $property=>$value){
        switch($property){
          case 'type' : 
            if(!preg_match('/[\w,{2,255}]/',$value)){
              $this->result['isValid'] = false;
              $this->result['messages'][$key][] = 'le type de module devrais etre une string.';
            }
            if(!in_array($value,$this->valiableModulesType)){
              $this->result['isValid'] = false;
              $this->result['messages'][$key][] = 'type de module non reconu.';
            }
            break;
          case 'module_content':
            dump($val->type);
            $this->moduleTypeValidators($val->type,$value);
        }
      }
    }
  }
  private function setValiableModulesType():array{
    return [
      'slider',

    ];
  }


  //! ce code sera potentielement dupliqué ... 
  //todo : le refacto dans ca propre class afin d'éviter ceci . 
  //! ces fonction devrons donc return des value afin des les envoyer a XxValidator (Categories/Pages(?)/Articles...)
  private function moduleTypeValidators($type,$val){
    switch($type){
      case 'slider':
        $this->validateSlider($val);
        break;
      default : 
        $this->result['isValid'] = false;
        $this->result['messages']['moduleValidation'][] = 'type de module non reconu.';
        break;
    }
  }

  private function validateSlider($slider){
    foreach($slider as $key=>$val){
      foreach($val as $skey=>$sval){
        switch($skey){
          case 'src' :
            if(!filter_var($sval,FILTER_VALIDATE_URL)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'url non valide.';
            }
            break;
        }
      }
    }
  }
}