<?php
namespace App\Service\Menu;

class MenuValidator {
  public function validateMenu(object $postData){
    $result = $this->getResult();
    if (empty($postData)) {
      $result['isValid'] = false;
      $result['messages'][] = 'postData empty';
      return $result;
    }
    if (gettype($postData) !== "object") {
      $result['isValid'] = false;
      $result['messages'][] = 'not an object';
      return $result;
    }
    foreach ($postData as $key => $val) {
      switch ($key) {
        case 'entries':
          if (gettype($val) !== 'array') {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'should be an array';
            continue;
          }
          foreach ($val as $skey => $sval) {
            foreach ($sval as $properti => $value) {
              switch ($properti) {
                case 'url':
                  if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $result['isValid'] = false;
                    $result['messages'][$key][] = [$properti => 'url non valide'];
                  }
                  break;
                case 'label':
                  if (!preg_match('/.{2,255}/', $value)) {
                    $result['isValid'] = false;
                    $result['messages'][$key][] = [$properti => 'le label doit faire entre 2 et 255 caractères'];
                  }
                  break;
              }
            }
          }
          break;
        case 'role':
          if (!in_array($val, ['ROLE_PUBLIC','ROLE_USER', 'ROLE_ADMIN', 'ROLE_MEMBER'])) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'role non géré';
          }
          break;
        default: 
          $result['isValid'] = false;
          $result['messages'][$key][] = 'Champ non gérer';
          break; 
      }
    }
    return $result;
  }
  public function validateEditMenu(object $postData){
    $result = $this->getResult();
    if (empty($postData)) {
      $result['isValid'] = false;
      $result['messages'][] = 'postData empty';
      return $result;
    }
    if (gettype($postData) !== "object") {
      $result['isValid'] = false;
      $result['messages'][] = 'not an object';
      return $result;
    }
    foreach ($postData as $key => $val) {
      switch ($key) {
        case 'entries':
          if (gettype($val) !== 'array') {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'should be an array';
            continue;
          }
          foreach ($val as $skey => $sval) {
            foreach ($sval as $properti => $value) {
              switch ($properti) {
                case 'url':
                  if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $result['isValid'] = false;
                    $result['messages'][$key][] = [$properti => 'url non valide'];
                  }
                  break;
                case 'label':
                  if (!preg_match('/.{2,255}/', $value)) {
                    $result['isValid'] = false;
                    $result['messages'][$key][] = [$properti => 'le label doit faire entre 2 et 255 caractères'];
                  }
                  break;
              }
            }
          }
          break;
        default: 
          $result['isValid'] = false;
          $result['messages'][$key][] = 'Champ non gérer';
          break; 
      }
    }
    return $result;
  }
  private function getResult(): array {
    return [
      'isValid'  => true,
      'messages' => [],
    ];
  }
}
