<?php
namespace App\Service;

class UserValidator{
  public function verifiUserDataCreate($postData){
    $result = [
      'isValid' => true,
      'messages'=> [],
    ];
    if(empty($postData)) return $result = ['isValid' => false, 'messages' => ['postData empty']];
    if(gettype($postData) !== "object") return $result = ['isValid' => false, 'messages'=> ['not an object']];
    if(
      empty($postData->email)
      ||empty($postData->password)
      // ||empty($postData->firstname)
      // ||empty($postData->lastname)
    ) return $result = ['isValid'=>false,'messages'=>['missing fields']];
    foreach($postData as $key=>$val){
      switch($key){
        case 'email':
          if(!filter_var($val,FILTER_VALIDATE_EMAIL)) 
          $result['isValid'] = false;
          $result['messages'] = ['Email invalide'];
        break;
        case 'password':
          if (!preg_match('/^.{8,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'] = ['Mot de passe invalide'];
        }
        break;
        case 'firstname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'] = ['Mot de passe invalide'];
        }
        break;
        case 'lastname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'] = ['Mot de passe invalide'];
        }
        break;
        default: 
          $result['isValid'] = false; 
          $result['messages'] = ['champ non gérer'];
        break;
      }
    }
    return $result;  
  }
}