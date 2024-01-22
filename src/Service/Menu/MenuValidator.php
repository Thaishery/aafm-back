<?php
namespace App\Service\Menu;

class MenuValidator {
  private $result;
  public function __construct()
  {
    $this->result = $this->getResult();
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
  
  private function getResult(): array {
    return [
      'isValid'  => true,
      'messages' => [],
    ];
  }

  private function validatePostDataLength(object $postData):void{
    if (empty($postData)) {
      $this->result['isValid'] = false;
      $this->result['messages'][] = 'postData empty';
    }
    if (gettype($postData) !== "object") {
      $this->result['isValid'] = false;
      $this->result['messages'][] = 'not an object';
    }
    if(count(get_object_vars(($postData))) > 5){
      $this->result['isValid'] = false;
      $this->result['messages']['lengthValidation'] = 'too much properties';
    }
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
