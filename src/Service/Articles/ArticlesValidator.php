<?php
namespace App\Service\Article;

use App\Service\DefaultValidator;
use App\Service\Modules\ModulesValidator;

class ArticlesValidator extends DefaultValidator {
  private $valiableModulesType;
  private $moduleValidator; 
  public function __construct()
  {
    parent::__construct();
    //? for now ... may be moved to it's own class to failitate maintnability .
    $this->moduleValidator = new ModulesValidator('articles'); 
    $this->valiableModulesType =  $this->moduleValidator->getValiableModulesType('articles');
    // $this->maxPost = 5;
  }

  public function validateArticle(object $postData){
    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;
    
    //? requiere fields detection here : 
    $requieredField = ["title","contenu","is_publish","id_categorie"];
    foreach($requieredField as $field){
      if (!array_key_exists($field, (array) $postData)){
        $this->result['isValid'] = false;
        $this->result['messages'][$field][] = 'ne peut être vide';
      }
    }

    foreach($postData as $key =>$val){
      switch ($key){
        case 'title' :
          if(!preg_match('/[\w,{2,255}]/',$val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Should be any acsi char from 2 to 255 char';
          }
          break;
        case 'contenu' : 
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
        case 'is_publish':
          if(!is_bool($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'is_publish devrais être un bool.';
          }
          break;
        case 'id_categorie':
          if(!is_numeric($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'id_categorie devrais être un number.'; 
            break;
          }
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
            if(!isset($val->type)){
              $this->result['isValid'] = false;
              $this->result['messages']['type'][] = 'type de module manquant.';
              break;
            }
            //? on utilise ModulesValidator qui vas renvoyer la même structure que $this->result et array_merge le resultat : 
            $result = $this->moduleValidator->moduleTypeValidators($val->type,$value);
            $this->result = array_merge($this->result, $result);
        }
      }
    }
  }

}