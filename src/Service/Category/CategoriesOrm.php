<?php
namespace App\Service\Category;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CategoryOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createCategory( object $postData):bool{
    try{
      $category = new Categories;
      $category->setName($postData->name);
      $category->setContent((array) $postData->content);
      $this->manager->persist($category);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function editCategory(Categories $category, object $postData):bool{
    try{
      if(isset($postData->name))$category->setName($postData->name);
      $category->setContent((array) $postData->content);
      $this->manager->persist($category);
      $this->manager->flush();
      return true; 
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function deleteCategory(Categories $category):bool{
    try{
      $this->manager->remove($category);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false; 
    }
    return false;
  }
}