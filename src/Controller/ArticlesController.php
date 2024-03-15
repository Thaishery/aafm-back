<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\User;
use App\Service\Article\ArticlesOrm;
use App\Service\Article\ArticlesValidator;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ArticlesController extends AbstractController
{

	private $roleChecker;
	private $articleValidator;

	public function __construct(){
		$this->roleChecker = new UserVerifiRole;
		$this->articleValidator = new ArticlesValidator;
	}

  #[Route('/api/public/articles', name: 'get_all_articles')]
  public function getAll(EntityManagerInterface $manager): JsonResponse
  {
		$articles = $manager->getRepository(Articles::class)->findBy(['is_publish'=>true],['created_at'=>'DESC']);
		if(!$articles) return $this->json(['message'=>'pas d\'articles trouver'], JsonResponse::HTTP_OK);
		$results = array();
		foreach ($articles as $key => $val) {
      if($val instanceof Articles){
					$results[]=$val->populate();
			}
    }
		return $this->json(['message'=>$results],JsonResponse::HTTP_OK);
  }

	#[Route('/api/public/articles/{id}', name: 'get_article_by_id', methods: 'GET')]
  public function getArticleById(EntityManagerInterface $manager,int $id): JsonResponse
  {
		$articles = $manager->getRepository(Articles::class)->findOneBy(['id'=>$id]);
		if(!$articles) return $this->json(['message'=>'pas d\'articles trouver'], JsonResponse::HTTP_OK);
		return $this->json(['message'=>$articles->populate()],JsonResponse::HTTP_OK);
  }
	
	#[Route('/api/public/articles/{name}/{title}', name: 'get_article_by_title', methods: 'GET')]
  public function getArticleByTitle(?Categories $categorie, string $title, EntityManagerInterface $manager): JsonResponse
  {
		if(!$categorie) return $this->json(['message'=>'Catégorie introuvable'],JsonResponse::HTTP_NO_CONTENT);
		$articles = $manager->getRepository(Articles::class)->findOneBy(['title'=>$title,'id_categorie'=>$categorie->getId()]);
		if(!$articles) return $this->json(['message'=>'pas d\'articles trouver'], JsonResponse::HTTP_NO_CONTENT);
		return $this->json(['message'=>$articles->populate()],JsonResponse::HTTP_OK);
  }

	#[Route('/api/auth/articles', name: 'add_article', methods:'POST')]
	public function addArticle(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager) :JsonResponse
	{
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->articleValidator->validateArticle($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
		$categorie = $manager->getRepository(Categories::class)->findOneById($postData->id_categorie);
		if(!$categorie) return $this->json(['message'=>'catégori non trouvée. '],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
		$articlesOrm = new ArticlesOrm($manager);
		$created = $articlesOrm->createArticle($postData,$categorie,$user);
		if(!$created) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
	}

	#[Route('/api/auth/articles/{id}', name: 'edit_article', methods:'PUT')]
	public function editArticle(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager, int $id) :JsonResponse
	{
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
		if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->articleValidator->validateArticle($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
		$categorie = $manager->getRepository(Categories::class)->findOneById($postData->id_categorie);
		if(!$categorie) return $this->json(['message'=>'catégori non trouvée.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
		$articles = $manager->getRepository(Articles::class)->findOneBy(['id'=>$id]);
		if(!$articles) return $this->json(['message'=>'pas d\'articles trouver'], JsonResponse::HTTP_OK);
		$articlesOrm = new ArticlesOrm($manager);
		$created = $articlesOrm->editArticle($articles,$categorie,$postData);
		if(!$created) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
	}
	#[Route('/api/auth/articles/{id}', name: 'delete_article', methods:'DELETE')]
	public function deleteArticle(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager, int $id) :JsonResponse
	{
		if(null == $id) return $this->json(['message' =>'Id manquant'], JsonResponse::HTTP_FORBIDDEN);
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
		$articles = $manager->getRepository(Articles::class)->findOneBy(['id'=>$id]);
		if(empty($articles)|| null == $articles) return $this->json(['message'=>'Article introuvable'], JsonResponse::HTTP_FORBIDDEN);
		$articlesOrm = new ArticlesOrm($manager);
		$deleted = $articlesOrm->deleteArticle($articles);
		if(!$deleted) return $this->json(['message'=>'Erreur lors de la suppression du Categories'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
	}
}
