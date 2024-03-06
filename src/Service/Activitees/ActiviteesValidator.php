<?php
namespace App\Service\Activitees;

use App\Service\DefaultValidator;
use DateTime;
use Exception;


class ActiviteesValidator extends DefaultValidator {
  public function __construct()
  {
    parent::__construct();
    //? we will maybe need to adapt this : 
    $this->maxPost = 6;
  }
  public function validateActivitees(object $postData){
    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;
    
    $requieredField = ["date", "nom","places","is_open"];
    foreach($requieredField as $field){
      if (!array_key_exists($field, (array) $postData)){
        $this->result['isValid'] = false;
        $this->result['messages'][$field][] = 'ne peut être vide';
      }
    }

    foreach($postData as $key =>$val){
      switch ($key){
        case 'date':
          if(!$this->isTimestamp($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Devrais être un timestamp';
            //? get out befor checking if date > now ... 
            break;
          }
          if(new DateTime('@'.$val)< new DateTime()){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Impossible de créer ou modifier un evenement pour une date passé, sauf si vous avez une machine a remonté le temps :thinking:';
          }
          break;
        case 'nom' :
          if(!preg_match('/[\w,{2,255}]/',$val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Should be any acsi char from 2 to 255 char';
          }
          break;
        case 'places':
          if(!is_int($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'devrais être un entier';
            break;
          }
          if($val <0){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Le nombre de place ne peut pas être negatif';
            break;
          }
          break;
        case 'is_open':
          if(!is_bool($val)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'dois être un bool';
          }
          break;
        default : 
          break;
      }
    }
    return $this->result;
  }

  /**
  * @param string $string
  * @return bool
  */
  public function isTimestamp(string $string)
  {
    try {
      new DateTime('@' . $string);
    } catch(Exception $e) {
      return false;
    }
    return true;
  }
}