<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Menu\MenuValidator;
use App\Service\User\UserVerifiRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MenusController extends AbstractController
{
	private $roleChecker;
	private $menuValidator;

	public function __construct(){
		$this->roleChecker = new UserVerifiRole;
		$this->menuValidator = new MenuValidator;
	}

  #[Route('/api/public/menu', name: 'public_menu', methods: 'GET')]
  public function getPublicMenu(): JsonResponse
  {
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/MenuController.php',
    ]);
  }

  #[Route('/api/auth/menu', name: 'auth_menu', methods: 'GET')]
  public function getAuthMenu(#[CurrentUser] ? User $user): JsonResponse
  {
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/MenuController.php',
    ]);
  }
  
	#[Route('/api/auth/menu', name: 'add_menu', methods:'POST')]
  public function addMenu(#[CurrentUser] ? User $user, Request $req): JsonResponse
  {
		if(!$this->roleChecker->checkRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->menuValidator->validateMenu($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
		//todo : Check if a menu for this role already exist, if it does update it. 
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/MenuController.php',
    ]);
  }
  
	#[Route('/api/auth/menu/{id}', name: 'edit_menu', methods:'PUT')]
  public function editMenu(#[CurrentUser] ? User $user, Request $req): JsonResponse
  {
		if(!$this->roleChecker->checkRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
		$postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);

    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/MenuController.php',
    ]);
  }
  
  #[Route('/api/auth/menu/{id}', name: 'delete_menu', methods:'DELETE')]
  public function deleteMenu(#[CurrentUser] ? User $user, Request $req): JsonResponse
  {
		if(!$this->roleChecker->checkRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);

    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/MenuController.php',
    ]);
  }

}
