<?php
namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
// use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserExternalManager{

  public function createOrPrepareExternalUser($userInfos,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $manager,JWTTokenManagerInterface $jwtManager){
    $existUser = $manager->getRepository(User::class)->findOneBy(['email'=>$userInfos->email]);
    //? si utilisateur inconu on le créer : 
    if(!$existUser){      
      $user = new User(); 
      if(empty($userInfos->email)) return false;
      $user->setIsInternal(false);
      $user->setRoles(['ROLE_USER']);
      $user->setEmail($userInfos->email);
      $generatedPassword = $this->generateRandomPassword();
      $user->setPassword($passwordHasher->hashPassword($user, $generatedPassword));
      unset($generatedPassword);
      if(!empty($userInfos->family_name)) $user->setFirstname($userInfos->family_name);
      if(!empty($userInfos->given_name)) $user->setLastname($userInfos->given_name);
      $manager->persist($user);
      $manager->flush();
      $existUser = $manager->getRepository(User::class)->findOneBy(['email'=>$userInfos->email]);
    }
    //? une fois celui-ci créer, on va générer un one time external_id : 
    if(!$existUser) return false; //? a ce stade une erreur est survenu... 
    $uuid = uniqid("");
    $existUser->setExternalId($uuid);
    $manager->persist($existUser);
    $manager->flush();
    return $uuid;
  }

  public function logExternalUser($uuid, EntityManagerInterface $manager, JWTTokenManagerInterface $jwtManager){
    //get user : 
    $user = $manager->getRepository(User::class)->findOneBy(['externalId'=>$uuid]);
    if(!$user)return false; 
    //generate token : 
    $response = [
      'user'=>$user->getUserIdentifier(),
      'token'=>$this->getToken($user,$jwtManager)
    ];
    //clean uuid token : 
    $user->setExternalId(null);
    $manager->persist($user);
    $manager->flush();
    //return result : 
    return $response;
  }

  public function getToken($user, JWTTokenManagerInterface $jwtManager){
    // $jwtManager = new JWTTokenManagerInterface();
    $token = $jwtManager->create($user);
    return $token;
  }

  public function generateRandomPassword(){
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.;:!§%ù*$¤$^¨_\\à@';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
  }
}