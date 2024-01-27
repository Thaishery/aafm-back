<?php
namespace App\Service\Modules;

//! Attention fonctionnement de ce validator un peu particulier, return le resultat qui dois être array merge par la classe appelante.
class ModulesValidator {
  private $result;
  private $valiableModulesType;

  public function __construct(){
    $this->result = [];
    $this->valiableModulesType = $this->setValiableModulesType();
  }

  public function getValiableModulesType():array{
    return $this->valiableModulesType;
  }

  private function setValiableModulesType():array{
    return [
      'slider',

    ];
  }

  public function moduleTypeValidators($type,$val):array{
    switch($type){
      case 'slider':
        if(!is_array($val)){
          $this->result['isValid'] = false;
          $this->result['messages']['moduleValidation'][] = 'Le slider devrais être un array.';
          break;
        }
        $this->validateSlider($val);
        break;
      case 'simpleText':
        dump($val);
        break;
      default : 
        $this->result['isValid'] = false;
        $this->result['messages']['moduleValidation'][] = 'type de module non reconu.';
        break;
    }
    return $this->result;
  }

  private function validateSlider($slider):void{
    foreach($slider as $key=>$val){
      foreach($val as $skey=>$sval){
        switch($skey){
          case 'src' :
            if(!filter_var($sval,FILTER_VALIDATE_URL)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'url non valide.';
            }
            break;
          case 'alt':
            if(!preg_match('/\w{0,255}/',$sval)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'texte alternatif non valide.';
            }
            break;
          case 'havecaptions':
            if(!is_bool($sval)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'devrais être un bool.';
            }
            break;
          case 'title': 
            if(!preg_match('/\w{0,255}/',$sval)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'le titre ne devrais comporter que des caractére textuels.<br>Il devrais aussi comporter entre 0 et 255 caractéres';
            }
            break;
          case 'desc':
            if(!preg_match('/\w/',$sval)){
              $this->result['isValid'] = false;
              $this->result['messages']['slider'][$skey][] = 'le titre ne devrais comporter que des caractére textuels.';
            }
            break;
          default :
            $this->result['isValid'] = false;
            $this->result['messages']['slider'][$skey][] = 'champ non gérer';
            break;
        }
      }
    }
  }
}