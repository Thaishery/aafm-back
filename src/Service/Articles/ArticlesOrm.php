<?php
namespace App\Service\Article;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ArticlesOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createArticle( object $postData):bool{
    try{
      $article = new Articles;
      $article->setTitle($postData->name);
      $article->setContenu((array) $postData->content);
      $this->manager->persist($article);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      return false;
    }
    return false;
  }

  public function editArticle(Articles $article, object $postData):bool{
    try{
      if(isset($postData->name))$article->setTitle($postData->name);
      $article->setContenu((array) $postData->content);
      $this->manager->persist($article);
      $this->manager->flush();
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
}