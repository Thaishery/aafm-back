<?php

namespace App\Tests\Service;

use App\Service\User\UserInternalCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserInternalCreatorTest extends KernelTestCase{
  private $entityManager;
  private $passwordHasher;

  protected function setUp(): void
  {
    parent::setUp();
    $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
  }

  public function testUserInternalCreatorCreateInternalUser(){
    $userCreator = new UserInternalCreator();
    $MinimalValid = [
      'email' => 'gdeb@gdeb.fr',
      'password' => 'p4$sW0rD1'
    ];
    $badEmail = [
      'email' => NULL,
      'password' => 'p4$sW0rD1'
    ];
    $badPassword = [
      'email' => 'gdeb@gdeb.fr',
      'password' => null,
    ];
    $alreadyExistingEmail = [
      'email' => 'alreadyExist@gdeb.fr',
      'password'=> 'p4$sW0rD1'
    ];
    $validWhitFistname = [
      'email' => 'gdeb1@gdeb.fr',
      'password' => 'p4$sW0rD1',
      'firstname' => 'Guillaume'
    ];
    $validWhitLastname = [
      'email' => 'gdeb2@gdeb.fr',
      'password' => 'p4$sW0rD1',
      'lastname' => 'DEBUCQUET'
    ];
    $fullValid = [
      'email' => 'gdeb3@gdeb.fr',
      'password' => 'p4$sW0rD1',
      'firstname' => 'Guillaume',
      'lastname' => 'DEBUCQUET'
    ];

    $this->assertEquals(true, $userCreator->createInternalUser((object)$fullValid, $this->passwordHasher, $this->entityManager),'fullValid');
    $this->assertEquals(true, $userCreator->createInternalUser((object)$validWhitFistname, $this->passwordHasher, $this->entityManager),'validWithFistName');
    $this->assertEquals(false, $userCreator->createInternalUser((object)$badEmail, $this->passwordHasher, $this->entityManager),'badEmail');
    $this->assertEquals(true, $userCreator->createInternalUser((object)$validWhitLastname, $this->passwordHasher, $this->entityManager),'validWithLastName');
    $this->assertEquals(false, $userCreator->createInternalUser((object)$alreadyExistingEmail, $this->passwordHasher, $this->entityManager),'alreadyExistingEmail');
    $this->assertEquals(true, $userCreator->createInternalUser((object)$MinimalValid, $this->passwordHasher, $this->entityManager),'minimalValid');
    $this->assertEquals(false, $userCreator->createInternalUser((object)$badPassword, $this->passwordHasher, $this->entityManager),'badPassword');
  }
}
