<?php

namespace App\Tests\Service;

use App\Service\UserValidator;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserValidatorTest extends KernelTestCase{
  public function testUserValidatorverifiUserData(){
    $userValidator = new UserValidator();
    $failOnEmail = [
      'email' => 'test',
      'password' => 'p4$sW0rD',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
    ];
    $failOnShortPassword = [
      'email' => 'gdeb@gdeb.fr',
      'password' => 'my',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
    ];
    $failOnFirstName = [
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD',
      'firstname'=>'Guill4ume',
      'lastname' =>'DEBUCQUET',
    ];
    $failOnLastName = [
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD',
      'firstname'=>'Guillaume',
      'lastname' =>'D3BUCQUET',
    ];
    $failOnFirstAndLastName=[
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD',
      'firstname'=>'Guill4ume',
      'lastname' =>'D3BUCQUET',
    ];
    $failOnEmptyEmail=[
      'password' => 'p4$sW0rD',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
    ];
    $failOnEmptyPassword=[
      'email' => 'gdeb@gdeb.fr',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
    ];
    $failOnAditionalField=[
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
      'aditional'=>'Should not be there'
    ];
    $valid = [
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD1',
      'firstname'=>'Guillaume',
      'lastname' =>'DEBUCQUET',
    ];
    
    //email mail formé: 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnEmail)['isValid']);
    //password trop cour: 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnShortPassword)['isValid']);
    //firstName invalide :
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnFirstName)['isValid']);
    //lastName invalide : 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnLastName)['isValid']);
    //first et lastname invalide : 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnFirstAndLastName)['isValid']);
    //empty Email : 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnEmptyEmail)['isValid']);
    //empty password : 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnEmptyPassword)['isValid']);
    //aditional field : 
    $this->assertEquals(false, $userValidator->verifiUserData((object)$failOnAditionalField)['isValid']);
    //valid Data  : 
    $this->assertEquals(true, $userValidator->verifiUserData((object)$valid)['isValid']);
  }
}