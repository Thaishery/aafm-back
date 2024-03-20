<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Entity\User;
use App\Service\Page\PageOrm;
use App\Service\Page\PagesValidator;
use App\Service\User\UserVerifiRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PagesController extends AbstractController
{
  private $roleChecker;
	private $pagesValidator;

	public function __construct(){
		$this->roleChecker = new UserVerifiRole;
		$this->pagesValidator = new PagesValidator;
	}
    
  #[Route('/', name: 'index')]
  public function index(): JsonResponse
  {
    return $this->json(['message' => 'I am woriking'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/public/pages/get_home_content', name: 'get_home_content')]
  public function getHomeContent(EntityManagerInterface $manager): JsonResponse
  {
    $pages = $manager->getRepository(Pages::class)->findOneBy(['name'=>'home']);
    if(empty($pages))return $this->json(['message'=>'Erreur lors du chargement des donées'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    if($pages instanceof Pages) $results= $pages->populate();
    return $this->json(['content'=>$results],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/pages/get_all', name: 'get_all_pages', methods:'GET')]
  public function getAllPages(#[CurrentUser] ? User $user, EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $pages = $manager->getRepository(Pages::class)->findAll();
    foreach($pages as $key=>$page){
      $pages[$key] = $page->populate();
    }
    return $this->json(['pages'=>$pages],Response::HTTP_OK);
  }

  #[Route('/api/auth/pages/create_new_page', name: 'create_new_page', methods:'POST')]
  public function createNewPage(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
    $isValid = $this->pagesValidator->validatePages($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $pages = $manager->getRepository(Pages::class)->findOneBy(['name'=>$postData->name]);
    if(!empty($pages)|| null !== $pages) return $this->json(['message'=>'Page deja existante'], JsonResponse::HTTP_FORBIDDEN);
    $pagesOrm = new PageOrm($manager);
    $created = $pagesOrm->createPage($postData);
    if(!$created) return $this->json(['message'=>'Erreur lors de la création de la page.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/public/pages/{name}', name: 'get_name_content',methods:'GET')]
  public function getNameContent(? Pages $pages , EntityManagerInterface $manager): JsonResponse
  {
    if(empty($pages))return $this->json(['message'=>'Erreur lors du chargement des donées'],JsonResponse::HTTP_NO_CONTENT);
    if($pages instanceof Pages) $results= $pages->populate();
    return $this->json(['content'=>$results],JsonResponse::HTTP_OK);
  }
  
  #[Route('/api/public/pages/{id}', name: 'get_page_by_id',methods:'GET')]
  public function getPageById(? Pages $pages , EntityManagerInterface $manager): JsonResponse
  {
    if(empty($pages))return $this->json(['message'=>'Erreur lors du chargement des donées'],JsonResponse::HTTP_NO_CONTENT);
    if($pages instanceof Pages) $results= $pages->populate();
    return $this->json(['content'=>$results],JsonResponse::HTTP_OK);
  }
  
  #[Route('/api/auth/pages/{id}', name: 'delete_page_by_id',methods:'DELETE')]
  public function deletePageById(#[CurrentUser] ? User $user,? Pages $pages , EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    if(empty($pages))return $this->json(['message'=>'Page non trouvé'],JsonResponse::HTTP_NO_CONTENT);
    // if($pages instanceof Pages) $results= $pages->populate();
    $pagesOrm = new PageOrm($manager);
    $pagesOrm->deletePage($pages);
    return $this->json(['message'=>'ok'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/pages/{id}', name: 'edit_page_by_id',methods:'POST')]
  public function editPageById(#[CurrentUser] ? User $user,? Pages $pages, Request $req , EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalides'], JsonResponse::HTTP_FORBIDDEN);
    $isValid = $this->pagesValidator->validatePages($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    // $pages = $manager->getRepository(Pages::class)->findOneBy(['name'=>'home']);
    if(empty($pages)|| null == $pages) return $this->json(['message'=>'Erreur lors du chargement des donées'], JsonResponse::HTTP_FORBIDDEN);
    $pagesOrm = new PageOrm($manager);
    $edited = $pagesOrm->editPage($pages,$postData);
    if(!$edited) return $this->json(['message'=>'Erreur lors de l\'édition du menu.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/pages/edit_home_content', name: 'edit_home_content', methods:'PUT')]
  public function editHomeContent(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
    $isValid = $this->pagesValidator->validatePages($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $pages = $manager->getRepository(Pages::class)->findOneBy(['name'=>'home']);
    if(empty($pages)|| null == $pages) return $this->json(['message'=>'Erreur lors du chargement des donées'], JsonResponse::HTTP_FORBIDDEN);
    $pagesOrm = new PageOrm($manager);
    $edited = $pagesOrm->editPage($pages,$postData);
    if(!$edited) return $this->json(['message'=>'Erreur lors de l\'édition du menu.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }



}
