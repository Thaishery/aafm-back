<?php
namespace App\Service\Category;

use App\Service\DefaultValidator;
use App\Service\Modules\ModulesValidator;

class CategoriesValidator extends DefaultValidator {
  private $valiableModulesType;
  private $moduleValidator; 
  public function __construct()
  {
    parent::__construct();
    //? for now ... may be moved to it's own class to failitate maintnability .
    $this->moduleValidator = new ModulesValidator(); 
    $this->valiableModulesType =  $this->moduleValidator->getValiableModulesType();
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
            //? on utilise ModulesValidator qui vas renvoyer la même structure que $this->result et array_merge le resultat : 
            $this->result = array_merge($this->result, $this->moduleValidator->moduleTypeValidators($val->type,$value));
        }
      }
    }
  }

}