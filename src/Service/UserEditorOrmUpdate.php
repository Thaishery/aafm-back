<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditorOrmUpdate{
  private $manager;
  
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  /**
   * postData already checked before this f call. 
   */
  public function updateUser(User $user, object $postData, UserPasswordHasherInterface $passwordHasher):bool{ 
    try{
      foreach($postData as $key=>$val){
        switch($key){
          case 'password':
            $user->setPassword($passwordHasher->hashPassword($user, $val));
            break;
          case 'firstname':
            $user->setFirstname($val);
            break;
          case 'lastname':
            $user->setLastname($val);
            break;
          default : 
           break;
        }        
      }
      //? save the updated user in db
      $this->manager->persist($user);
      $this->manager->flush();
      return true; 
    }catch(Exception $error){
      //TODO : Should log there :thinking:
      return false; 
    }
  }
}