<?php
namespace App\Service\Menu;

use App\Entity\Menus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MenuOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createMenu( object $postData):bool{
    try{
      $menu = new Menus;
      $menu->setEntries($postData->entries);
      $menu->setRole($postData->role);
      $this->manager->persist($menu);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function editMenu(Menus $menu, object $postData):bool{
    try{
      $menu->setEntries($postData->entries);
      $this->manager->persist($menu);
      $this->manager->flush();
      return true; 
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function deleteMenu(Menus $menu):bool{
    try{
      $this->manager->remove($menu);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false; 
    }
    return false;
  }
}