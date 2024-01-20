<?php

namespace App\Controller;

use App\Entity\Menus;
use App\Entity\User;
use App\Service\Menu\MenuOrm;
use App\Service\Menu\MenuValidator;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
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
  public function getPublicMenu(EntityManagerInterface $manager): JsonResponse
  {
		$existingMenu = $manager->getRepository(Menus::class)->findOneBy(['Role' => 'ROLE_PUBLIC']);
		if(!$existingMenu) return $this->json(['message'=>'Menu non trouvé'], JsonResponse::HTTP_OK);
    return $this->json(['entries'=>$existingMenu->getEntries(),'role'=>$existingMenu->getRole()],JsonResponse::HTTP_OK);
  }

	#[Route('/api/auth/menu', name: 'auth_public_menu', methods: 'GET')]
  public function getAuthPublicMenu(EntityManagerInterface $manager): JsonResponse
  {
		$existingMenu = $manager->getRepository(Menus::class)->findOneBy(['Role' => 'ROLE_PUBLIC']);
		if(!$existingMenu) return $this->json(['message'=>'Menu non trouvé'], JsonResponse::HTTP_OK);
    return $this->json(['entries'=>$existingMenu->getEntries(),'role'=>$existingMenu->getRole()],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/menu/{role}', name: 'auth_menu', methods: 'GET')]
  public function getAuthMenu(#[CurrentUser] ? User $user,EntityManagerInterface $manager,string $role): JsonResponse
  {
		if('string' !== gettype($role))return $this->json(['message'=>'devrais être une string'], JsonResponse::HTTP_FORBIDDEN);
		if(!$this->roleChecker->checkUserHaveRole(strtoupper($role), $user))return $this->json(['message'=>'Vous devez poseder ce role pour voir le menu'], JsonResponse::HTTP_FORBIDDEN);
		$existingMenu = $manager->getRepository(Menus::class)->findOneBy(['Role' => $role]);
		if(!$existingMenu) return $this->json(['message'=>'Menu non trouvé'], JsonResponse::HTTP_OK);
    return $this->json(['entries'=>$existingMenu->getEntries(),'role'=>$existingMenu->getRole()],JsonResponse::HTTP_OK);
  }
  
	#[Route('/api/auth/menu', name: 'add_menu', methods:'POST')]
  public function addMenu(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager): JsonResponse
  {
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->menuValidator->validateMenu($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);

		$existingMenu = $manager->getRepository(Menus::class)->findOneBy(['Role' => $postData->role]);
		if($existingMenu) return $this->json(['message'=>$existingMenu->getId()],JsonResponse::HTTP_ACCEPTED);
		
		$menuOrm = new MenuOrm($manager);
		$created = $menuOrm->createMenu($postData);
		if(!$created) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }
  
	#[Route('/api/auth/menu/{id}', name: 'edit_menu', methods:'PUT')]
  public function editMenu(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager, int $id): JsonResponse
  {
		if(null == $id) return $this->json(['message' =>'Id manquant'], JsonResponse::HTTP_FORBIDDEN);
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
		$postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->menuValidator->validateEditMenu($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
		$menu = $manager->getRepository(Menus::class)->findOneBy(['id'=>$id]);
		if(empty($menu)|| null == $menu) return $this->json(['message'=>'menu introuvable'], JsonResponse::HTTP_FORBIDDEN);
		$menuOrm = new MenuOrm($manager);
		$edited = $menuOrm->editMenu($menu,$postData);
		if(!$edited) return $this->json(['message'=>'Erreur lors de l\'édition du menu.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }
  
  #[Route('/api/auth/menu/{id}', name: 'delete_menu', methods:'DELETE')]
  public function deleteMenu(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager, int $id): JsonResponse
  {
		if(null == $id) return $this->json(['message' =>'Id manquant'], JsonResponse::HTTP_FORBIDDEN);
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
		$menu = $manager->getRepository(Menus::class)->findOneBy(['id'=>$id]);
		if(empty($menu)|| null == $menu) return $this->json(['message'=>'menu introuvable'], JsonResponse::HTTP_FORBIDDEN);
		$menuOrm = new MenuOrm($manager);
		$deleted = $menuOrm->deleteMenu($menu);
		if(!$deleted) return $this->json(['message'=>'Erreur lors de la suppression du menu'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

}
