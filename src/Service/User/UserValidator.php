<?php
namespace App\Service\User;

class UserValidator{

  public function verifiUserDataCreate($postData){
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
    if(
      empty($postData->email)
      ||empty($postData->password)
      ||empty($postData->passwordVerif)
      ) {
        $result['isValid'] = false;
        $result['messages'][] ='missing fields';
        return $result;
      }
    foreach($postData as $key=>$val){
      switch($key){
        case 'email':
          if(!filter_var($val,FILTER_VALIDATE_EMAIL)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'Email invalide';
          }
        break;
        case 'password':
          if (!preg_match('/^.{8,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'Mot de passe invalide';
        }
        break;
        case 'passwordVerif':
          if($val !== $postData->password){
            $result['isValid'] = false;
            $result['messages'][$key][] = 'les mots de passes ne sont pas identiques';
          }
        break;
        case 'firstname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'firstname invalide';
        }
        break;
        case 'lastname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'lastname invalide';
        }
        break;
        default: 
          $result['isValid'] = false; 
          $result['messages']['default'][] = 'champ non gérer';
        break;
      }
    }
    return $result;  
  }

  public function verifiUserDataEdit($postData){
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
    if(empty($postData->email)) return $result = ['isValid' => false, 'messages'=> ['missing email, we need it to update the user.']];
    foreach($postData as $key=>$val){
      switch($key){
        case 'email': 
          if(!filter_var($val,FILTER_VALIDATE_EMAIL)){
            $result['isValid'] = false;
            $result['messages'][$key][] = 'Email invalide';
          }
          break;
        case 'password':
          if(empty($postData->passwordVerif)){
            $result['isValid'] = false;
            $result['messages'] = ['passwordVerif'=>'la vérification du mot de passe ne peut-être vide'];
          }
          if (!preg_match('/^.{8,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'Mot de passe invalide';
          }
          break;
        case 'passwordVerif':
          if(empty($postData->password)){
            $result['isValid'] = false;
            $result['messages'] = ['password'=>'le mot de passe ne peut être vide'];
          }
          if($val !== $postData->password){
            $result['isValid'] = false;
            $result['messages'][$key][] = 'les mots de passes ne sont pas identiques';
          }
          break;
        case 'firstname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'firstname invalide';
          }
          break;
        case 'lastname':
          if (!preg_match('/^[\w\'\-,.][^0-9_!¡?÷?¿\/\\+=@#$%^ˆ&*(){}|~<>;:[\]]{2,}$/', $val)) {
            $result['isValid'] = false;
            $result['messages'][$key][] = 'lastname invalide';
          }
          break;
        default: 
          $result['isValid'] = false; 
          $result['messages']['default'][] = 'champ non gérer';
          break;
      }
    }
    return $result;  
  }
  
  private function getResult():array{
    return [
      'isValid' => true,
      'messages'=> [],
    ];
  }
}