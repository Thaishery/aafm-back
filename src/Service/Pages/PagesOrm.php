<?php
namespace App\Service\Page;

use App\Entity\Pages;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

use function Symfony\Component\Clock\now;

class PageOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createPage( object $postData):bool{
    try{
      $page = new Pages;
      $page->setName($postData->name);
      isset($postData->description)?$page->setDescription($postData->description):null;
      $page->setContent((array) $postData->content);
      $page->setCreatedAt(now());
      $this->manager->persist($page);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function editPage(Pages $page, object $postData):bool{
    try{
      if(isset($postData->name))$page->setName($postData->name);
      if(isset($postData->description))$page->setDescription($postData->description);
      $page->setContent((array) $postData->content);
      $page->setEditedAt(now());
      $this->manager->persist($page);
      $this->manager->flush();
      return true; 
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function deletePage(Pages $page):bool{
    try{
      $this->manager->remove($page);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false; 
    }
    return false;
  }
}