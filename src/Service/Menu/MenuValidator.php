<?php
namespace App\Service\Menu;

use App\Service\DefaultValidator;

class MenuValidator extends DefaultValidator {
  public function __construct()
  {
    parent::__construct();
  }
  public function validateMenu(object $postData){
    $this->validatePostDataLength($postData);

    if(!$this->result['isValid']) return $this->result;
    
    foreach ($postData as $key => $val) {
      switch ($key) {
        case 'entries':
          if (gettype($val) !== 'array') {
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'should be an array';
            //? Break ici car on ne veux pas poursuivre vers le traitement parseEntrieData (on enverais autre chose qu'un tacbleau ... )
            break;
          }
          $this->parseEntriesData($val,$key);
          break;
        case 'role':
          if (!in_array($val, ['ROLE_PUBLIC','ROLE_USER', 'ROLE_ADMIN', 'ROLE_MEMBER'])) {
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'role non géré';
          }
          break;
        default: 
          $this->result['isValid'] = false;
          $this->result['messages'][$key][] = 'Champ non gérer';
          break; 
      }
    }
    return $this->result;
  }
  
  public function validateEditMenu(object $postData){
    $this->validatePostDataLength($postData);

    if(!$this->result['isValid']) return $this->result;

    foreach ($postData as $key => $val) {
      switch ($key) {
        case 'entries':
          if (gettype($val) !== 'array') {
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'should be an array';
            break;
          }
          $this->parseEntriesData($val,$key);
          break;
        default: 
          $this->result['isValid'] = false;
          $this->result['messages'][$key][] = 'Champ non gérer';
          break; 
      }
    }
    return $this->result;
  }
  
  private function parseEntriesData($entries, $parent):void{
    foreach ($entries as $skey => $sval) {
      foreach ($sval as $properti => $value) {
        switch ($properti) {
          case 'url':
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
              $this->result['isValid'] = false;
              $this->result['messages'][$parent][] = [$properti => 'url non valide'];
            }
            break;
          case 'label':
            if (!preg_match('/.{2,255}/', $value)) {
              $this->result['isValid'] = false;
              $this->result['messages'][$parent][] = [$properti => 'le label doit faire entre 2 et 255 caractères'];
            }
            break;
        }
      }
    }
  }
}
