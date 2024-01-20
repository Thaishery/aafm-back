<?php
namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserEditorRoleValidator{
  private $manager;
  private $userToEdit;

  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function verifiUserEditPermision(User $user, object $postData):bool{
    //? can't find user : 
    if(!$this->verifiExistingUser($postData))return false;
    //? set the user : 
    $this->setUserToEdit($postData);
    // assert one of this or return false : 
    if($user->getEmail() === $this->userToEdit->getEmail()) return true;
    else if(in_array('ROLE_ADMIN',$user->getRoles())) return true;
    return false;
  }

  public function verifiExistingUser(object $postData):bool{
    if(null == $this->manager->getRepository(User::class)->findOneBy(['email' => $postData->email])) return false;
    return true; 
  }

  public function getUserToEdit(){
    return $this->userToEdit;
  }

  private function setUserToEdit(object $postData){
    $this->userToEdit = $this->manager->getRepository(User::class)->findOneBy(['email' => $postData->email]);
  }
}