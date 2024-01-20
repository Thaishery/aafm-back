<?php
namespace App\Service\User;

use App\Entity\User;

class UserVerifiRole{
  public function checkRole(string $role,User $user):bool{
    if(in_array($role,$user->getRoles()))return true;
    return false; 
  }
}