<?php
namespace App\Service;

use App\Entity\User;
// use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserInternalCreator{

  public function createInternalUser($postData,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $manager){
    $user = new User(); 
    if(empty($postData->email)) return false;
    if(empty($postData->password)) return false;
    if(!empty($manager->getRepository(User::class)->findOneBy(['email' => $postData->email]))) return false;
    $user->setIsInternal(true);
    $user->setRoles(['ROLE_USER']);
    $user->setEmail($postData->email);
    $user->setPassword($passwordHasher->hashPassword($user, $postData->password));
    if(!empty($postData->firstname)) $user->setFirstname($postData->firstname);
    if(!empty($postData->lastname)) $user->setLastname($postData->lastname);
    $manager->persist($user);
    $manager->flush();
    return true;
  }
}