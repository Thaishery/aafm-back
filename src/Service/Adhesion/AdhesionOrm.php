<?php
namespace App\Service\Adhesion;

use App\Entity\Adhesion;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

use function Symfony\Component\Clock\now;

class AdhesionOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createAdhesion( object $postData,User $user):bool{
    try{
      $adhesion = new Adhesion;
      $adhesion->setStatut($postData->statut);
      $adhesion->setIsPaid($postData->is_paid);
      if(isset($postData->commentaire))$adhesion->setCommentaire($postData->commentaire);
      $adhesion->setUser($user);
      $this->manager->persist($adhesion);
      $this->manager->flush();
      $this->manageStatutAndRole($adhesion);
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function editAdhesion(Adhesion $adhesion, object $postData):bool{
    try{
      if(isset($postData->date))$adhesion->setDate($postData->date);
      if(isset($postData->statut))$adhesion->setStatut($postData->statut);
      if(isset($postData->is_paid))$adhesion->setIsPaid($postData->is_paid);
      if(isset($postData->commentaire))$adhesion->setCommentaire($postData->commentaire);
      $this->manager->persist($adhesion);
      $this->manager->flush();
      $this->manageStatutAndRole($adhesion);
      return true; 
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function deleteArticle(Articles $article):bool{
    try{
      $this->manager->remove($article);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false; 
    }
    return false;
  }

  private function manageStatutAndRole(Adhesion $adhesion){
    $userToEdit = $adhesion->getUser();
    $userRoles = $userToEdit->getRoles();
    if($adhesion->isIsPaid()){
      if(!in_array('ROLE_MEMBER',$userRoles)) {
        array_push($userRoles,'ROLE_MEMBER');
      }
    }
    if(!$adhesion->isIsPaid()){
      if(in_array('ROLE_MEMBER',$userRoles)){
        array_splice($userRoles,array_search('ROLE_MEMBER',$userRoles),1);
      }
    }
    $userToEdit->setRoles($userRoles);
    $this->manager->persist($userToEdit);
    $this->manager->flush();
    unset($userToEdit, $userRoles);
  }
}