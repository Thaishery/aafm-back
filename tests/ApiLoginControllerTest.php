<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Controller\ApiLoginController;
use App\Service\UserInternalCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends KernelTestCase
{
  private $apiLoginController;
  private $entityManager;
  private $passwordHasher;
  private $userCreator;
  
  protected function setUp(): void
  {
    parent::setUp();
    $this->apiLoginController = self::getContainer()->get(ApiLoginController::class);
    $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    $this->userCreator = self::getContainer()->get(UserInternalCreator::class);
  }

  public function testLoginSuccess(){
    $user =  $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'alreadyExist@gdeb.fr']);
    $response = $this->apiLoginController->login($user);
    $this->assertInstanceOf(JsonResponse::class,$response);
    $data = json_decode($response->getContent(),true);
    $this->assertEquals('alreadyExist@gdeb.fr', $data['user']);
    $this->assertObjectHasProperty('token', (object)$data);
  }

  public function testLoginFail(){
    $response = $this->apiLoginController->login(null);
    $this->assertInstanceOf(JsonResponse::class,$response);
    $data = json_decode($response->getContent(),true);
    $this->assertEquals('missing credentials', $data['message']);
  }

  public function testRegisterFailEmptyData()
  {
    // Create a new Request object with your data
    $request = new Request();
    $response = $this->apiLoginController->registerUser($request,$this->passwordHasher,$this->entityManager,$this->userCreator);
    $this->assertInstanceOf(JsonResponse::class,$response);
    $statusCode = $response->getStatusCode();
    $data = json_decode($response->getContent(),true);
    $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $statusCode);
    $this->assertEquals('Données invalide',$data['message']);
  }
  
  public function testRegisterFailValidation()
  {
    $postData = [
      'email' => 'gdeb.fr',
      'password' => 'MyPassword'
    ];
    $jsonContent = json_encode($postData);
    $request = Request::create('/api/users/internal/register', 'POST', [], [], [], [], $jsonContent);
    $response = $this->apiLoginController->registerUser($request,$this->passwordHasher,$this->entityManager,$this->userCreator);
    $this->assertInstanceOf(JsonResponse::class,$response);
    $statusCode = $response->getStatusCode();
    $data = json_decode($response->getContent(),true);
    $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $statusCode);
    $this->assertEquals('Email invalide',$data[0]);
  }

  public function testRegisterSuccess()
  {
    $postData = [
      'email' => 'test1@gdeb.fr',
      'password' => 'MyPassword'
    ];
    $jsonContent = json_encode($postData);
    $request = Request::create('/api/users/internal/register', 'POST', [], [], [], [], $jsonContent);
    $response = $this->apiLoginController->registerUser($request,$this->passwordHasher,$this->entityManager,$this->userCreator);
    $this->assertInstanceOf(JsonResponse::class,$response);
    $statusCode = $response->getStatusCode();
    $data = json_decode($response->getContent(),true);
    $this->assertEquals(JsonResponse::HTTP_OK, $statusCode);
  }
}