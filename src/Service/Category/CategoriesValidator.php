<?php
namespace App\Service\Category;

class CategoriesValidator {
  private $result;
  public function __construct()
  {
    $this->result = $this->getResult();
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

  private function getResult(): array {
    return [
      'isValid'  => true,
      'messages' => [],
    ];
  }
}