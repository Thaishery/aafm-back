<?php
namespace App\Service\Adhesion;

use App\Service\DefaultValidator;


class AdhesionValidator extends DefaultValidator {

  public function validateAdhesion(object $postData){
    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;
    
    //? requiere fields detection here : 
    $requieredField = ["statut","is_paid"];
    foreach($requieredField as $field){
      if (!array_key_exists($field, (array) $postData)){
        $this->result['isValid'] = false;
        $this->result['messages'][$field][] = 'ne peut être vide';
      }
    }

    foreach($postData as $key =>$val){
      switch ($key){
        case 'date' :
          if(!$this->isTimestamp($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'date devrais être un timestamp';
          }
          break;
        case 'statut' : 
          if(!in_array($val,['pending','valide','expired'])){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'statut non reconu';
          }
          break;
        case 'is_paid':
          if(!is_bool($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'is_publish devrais être un bool.';
          }
          break;
        case 'commentaire':
          break;
        case 'user_id':
          break;
        default:
          break;
      }
    }
    return $this->result;

  }
} 