<?php
namespace App\Service\Activitees;

use App\Entity\Activitees;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Exception;

use function Symfony\Component\Clock\now;

class ActiviteesOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createActivitees( object $postData):bool{
    try{
      $activitees = new Activitees;
      $activitees->setDate(new DateTime('@' . $postData->date));
      $activitees->setNom($postData->nom);
      $activitees->setPlaces($postData->places);
      $activitees->setIsOpen($postData->is_open);
      if(isset($postData->description)) $activitees->setDescription($postData->description);
      if(isset($postData->lieu))$activitees->setLieu($postData->lieu);
      $this->manager->persist($activitees);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }
  public function editActivitees( object $postData,$activite):bool{
    try{
      $activite->setDate(new DateTime('@' . $postData->date));
      $activite->setNom($postData->nom);
      $activite->setPlaces($postData->places);
      $activite->setIsOpen($postData->is_open);
      if(isset($postData->description)) $activite->setDescription($postData->description);
      if(isset($postData->lieu))$activite->setLieu($postData->lieu);
      $this->manager->persist($activite);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function addParticipant(User $user,Activitees $activitees){
    try{
      $users = $activitees->getUser();
      $already_in = false;
      foreach($users as $activiteeUsers){
        if($activiteeUsers->getId() == $user->getId())$already_in = true;
      }
      if($already_in)return true;
      $activitees->addUser($user);
      $this->manager->persist($activitees);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
     return false;
    }
  }

  public function deleteParticipant(User $user,Activitees $activitees){
    try{
      $users = $activitees->getUser();
      $already_in = false;
      foreach($users as $activiteeUsers){
        if($activiteeUsers->getId() == $user->getId())$already_in = true;
      }
      if(!$already_in)return true;
      $activitees->removeUser($user);
      $this->manager->persist($activitees);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
     return false;
    }
  }

  // public function editActivitees(Activitees $activitees, object $postData):bool{
  //   try{
  //     if(isset($postData->name))$activitees->setName($postData->name);
  //     if(isset($postData->description))$activitees->setDescription($postData->description);
  //     $activitees->setContent((array) $postData->content);
  //     $activitees->setEditedAt(now());
  //     $this->manager->persist($activitees);
  //     $this->manager->flush();
  //     return true; 
  //   }catch(Exception $error){
  //     return false;
  //   }
  //   return false;
  // }

  public function deleteActivitees(Activitees $activitees):bool{
    try{
      $members = $activitees->getUser();
      foreach($members as $member){
        $activitees->removeUser($member);
      }
      $this->manager->remove($activitees);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false; 
    }
    return false;
  }
}