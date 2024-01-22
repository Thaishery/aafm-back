<?php
namespace App\Service\User;

class UserValidator{
  private $result;

  public function __construct()
  {
    $this->result = $this->getResult();
  }

  public function verifiUserDataCreate($postData){

    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;
    if(
      empty($postData->email)
      ||empty($postData->password)
      ||empty($postData->passwordVerif)
      ) {
        $this->result['isValid'] = false;
        $this->result['messages'][] ='missing fields';
        return $this->result;
      }
    $this->validateDatas($postData);
    return $this->result;  
  }

  public function verifiUserDataEdit($postData){
    $this->validatePostDataLength($postData);
    if(!$this->result['isValid']) return $this->result;
    if(empty($postData->email)) {
      $this->result['isValid'] = false;
      $this->result['messages'][] ='missing email, we need it to update the user.';
      return $this->result;
    };
    $this->validateDatas($postData);
    return $this->result;  
  }
  
  private function getResult():array{
    return [
      'isValid' => true,
      'messages'=> [],
    ];
  }

  private function validateDatas($postData):void{
    foreach($postData as $key=>$val){
      switch($key){
        case 'email': 
          if(!filter_var($val,FILTER_VALIDATE_EMAIL)){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Email invalide';
          }
          break;
        case 'password':
          if(empty($postData->passwordVerif)){
            $this->result['isValid'] = false;
            $this->result['messages'] = ['passwordVerif'=>'la vérification du mot de passe ne peut-être vide'];
          }
          if (!preg_match('/^.{8,}$/', $val)) {
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'Mot de passe invalide';
          }
          break;
        case 'passwordVerif':
          if(empty($postData->password)){
            $this->result['isValid'] = false;
            $this->result['messages'] = ['password'=>'le mot de passe ne peut être vide'];
          }
          if(!empty($postData->password) && $val !== $postData->password){
            $this->result['isValid'] = false;
            $this->result['messages'][$key][] = 'les mots de passes ne sont pas identiques';
          }
          break;
        case 'firstname':
          $this->validateName($val,$key);
          break;
        case 'lastname':
          $this->validateName($val,$key);
          break;
        default: 
          $this->result['isValid'] = false; 
          $this->result['messages']['default'][] = 'champ non gérer';
          break;
      }
    }
  }

  private function validateName($name,$key):void{
    if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $name)) {
      $this->result['isValid'] = false;
      $this->result['messages'][$key][] = $key.' invalide';
    }
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
}