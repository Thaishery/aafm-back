<?php
namespace App\Service\Article;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

use function Symfony\Component\Clock\now;

class ArticlesOrm {

  private $manager; 
  
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  public function createArticle( object $postData, Categories $categories, User $user):bool{
    try{

      $article = new Articles;
      $article->setTitle($postData->title);
      $article->setContenu((array) $postData->contenu);
      $article->setIdCategorie($categories);
      $article->setIsPublish($postData->is_publish);
      $article->setCreatedAt(now());
      $article->setAuteur($user);
      $this->manager->persist($article);
      $this->manager->flush();
      return true;
    }catch(Exception $error){
      dump($error);
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