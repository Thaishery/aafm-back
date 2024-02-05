<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\User;
use App\Service\Category\CategoryOrm;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Category\CategoriesValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CategoriesController extends AbstractController
{
  private $roleChecker;
	private $categoriesValidator;

	public function __construct(){
		$this->roleChecker = new UserVerifiRole;
		$this->categoriesValidator = new CategoriesValidator;
	}

  #[Route('/api/public/categories', name: 'get_all_categories', methods: 'GET')]
  public function getAllCategories(EntityManagerInterface $manager): JsonResponse
  {
    $categories = $manager->getRepository(Categories::class)->findAll();
    if(!$categories) return $this->json(['message'=>'Catégorie non trouvé'], JsonResponse::HTTP_OK);
    $results = array();
    foreach ($categories as $key => $val) {
      if($val instanceof Categories)$results[]=$val->populate();
    }
    return $this->json(['categories'=>$results],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/categories', name: 'add_category', methods: 'POST')]
  public function addCategory(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager): JsonResponse
  {
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->categoriesValidator->validateCategory($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);

		$existingCategory = $manager->getRepository(Categories::class)->findOneBy(['name' => $postData->name]);
		if($existingCategory) return $this->json(['message'=>$existingCategory->getId()],JsonResponse::HTTP_ACCEPTED);
		
		$categoryOrm = new CategoryOrm($manager);
		$created = $categoryOrm->createCategory($postData);
		if(!$created) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/categories/{id}', name: 'edit_categories', methods:'PUT')]
  public function editMenu(#[CurrentUser] ? User $user, Request $req, EntityManagerInterface $manager, int $id): JsonResponse
  {
		if(null == $id) return $this->json(['message' =>'Id manquant'], JsonResponse::HTTP_FORBIDDEN);
		if(!$this->roleChecker->checkUserHaveRole('ROLE_ADMIN', $user))return $this->json(['message'=>'Interdis'], JsonResponse::HTTP_FORBIDDEN);
		$postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
    $isValid = $this->categoriesValidator->validateCategory($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
		$categories = $manager->getRepository(Categories::class)->findOneBy(['id'=>$id]);
		if(empty($categories)|| null == $categories) return $this->json(['message'=>'catégorie introuvable'], JsonResponse::HTTP_FORBIDDEN);
		$categoriesOrm = new CategoryOrm($manager);
		$edited = $categoriesOrm->editCategory($categories,$postData);
		if(!$edited) return $this->json(['message'=>'Erreur lors de l\'édition du menu.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

  // #[Route('/categories', name: 'app_categories')]
  // public function index(): JsonResponse
  // {
  //   return $this->json([
  //     'message' => 'Welcome to your new controller!',
  //     'path' => 'src/Controller/CategoriesController.php',
  //   ]);
  // }
}
