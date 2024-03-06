<?php
namespace App\Service;

use DateTime;
use Exception;

class DefaultValidator {
  
  protected $result;
  protected $maxPost;

  public function __construct()
  {
    $this->result = $this->getResult();
    $this->maxPost = 5;
  }

  protected function validatePostDataLength(object $postData):void{
    if (empty($postData)) {
      $this->result['isValid'] = false;
      $this->result['messages'][] = 'postData empty';
    }
    if (gettype($postData) !== "object") {
      $this->result['isValid'] = false;
      $this->result['messages'][] = 'not an object';
    }
    if(count(get_object_vars(($postData))) > $this->maxPost){
      $this->result['isValid'] = false;
      $this->result['messages']['lengthValidation'] = 'too much properties';
    }
  }

  protected function getResult(): array {
    return [
      'isValid'  => true,
      'messages' => [],
    ];
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