<?php
namespace App\Service\User;

use App\Entity\User;

class UserVerifiRole{
  public function checkUserHaveRole(string $role,User $user):bool{
    
    $rolesHierarchie = $this->getRolesHierarchie();
    if(!in_array($role,$rolesHierarchie)) return false;
    $maxRole = 0;
    foreach($user->getRoles() as $key=>$val){
      if(!in_array($val,$rolesHierarchie))continue;
      $curRole = array_search($val,$rolesHierarchie);
      if($curRole>$maxRole) $maxRole = $curRole;
    }
    if($maxRole >= array_search($role,$rolesHierarchie))return true;
    return false; 
  }
  private function getRolesHierarchie(){
    return [
      0=>'ROLE_PUBLIC',
      1=>'ROLE_USER',
      2=>'ROLE_MEMBER',
      3=>'ROLE_ADMIN'
    ];
  }
}