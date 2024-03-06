<?php

namespace App\Controller;

use App\Entity\Adhesion;
use App\Entity\User;
use App\Service\Adhesion\AdhesionOrm;
use App\Service\Adhesion\AdhesionValidator;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AdhesionController extends AbstractController
{
  private $roleChecker;
	private $adhesionValidator;

  public function __construct(){
		$this->roleChecker = new UserVerifiRole;
		$this->adhesionValidator = new AdhesionValidator;
	}

  #[Route('/api/auth/adhesion', name: 'get_current_user_adhesion', methods:'GET')]
  public function getCurrentUserAdhesion(#[CurrentUser]? User $user,EntityManagerInterface $manager): JsonResponse
  {
    $adhesion = $manager->getRepository(Adhesion::class)->findOneBy(['user'=>$user]);
    if(!$adhesion)return $this->json(['message'=>'Adhesion non retrouvé']);
    return $this->json($adhesion->populate(),Response::HTTP_OK);
  }

  // #[Route('/api/auth/adhesion/{id}', name: 'get_adhesion_by_id', methods:'GET')]
  // public function getUserAdhesionById(#[CurrentUser]? User $user,?Adhesion $adhesion,EntityManagerInterface $manager): JsonResponse
  // {
  //   dump($adhesion);
  //   if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)||$user !== $adhesion->getUser())return $this->json(['message'=>'Droits Insufisants'],Response::HTTP_FORBIDDEN);
  //   if(!$adhesion)return $this->json(['message'=>'Adhesion non retrouvé']);
  //   return $this->json($adhesion->populate(),Response::HTTP_OK);
  // }

  #[Route('/api/auth/adhesion/user/{id}', name: 'get_adhesion_by_user_id', methods:'GET')]
  public function getUserAdhesionByUserId(#[CurrentUser]? User $user,?User $userToGet,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)||$user !== $userToGet)return $this->json(['message'=>'Droits Insufisants'],Response::HTTP_FORBIDDEN);
    $adhesion = $manager->getRepository(Adhesion::class)->findOneBy(['user'=>$userToGet]);
    if(!$adhesion)return $this->json(['message'=>'Adhesion non retrouvé']);
    return $this->json($adhesion->populate(),Response::HTTP_OK);
  }


  
  #[Route('/api/auth/adhesion', name: 'add_current_user_adhesion', methods:'POST')]
  public function addCurrentUserAdhesion(#[CurrentUser]? User $user,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    $tempory = json_decode($req->getContent(), false);
    $postData = new stdClass();
    $postData->statut = 'pending';
    $postData->is_paid = false;
    if(isset($tempory->commentaire)) $postData->commentaire = $tempory->commentaire;
    if($this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)){
      if($tempory||!empty($tempory)){
        foreach($tempory as $key=>$val){
          $postData->$key = $val;
        }
      }
    }
    unset($tempory);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->adhesionValidator->validateAdhesion($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $adhesion = $manager->getRepository(Adhesion::class)->findOneBy(['user'=>$user->getId()]);
		if($adhesion) return $this->json(['message'=>'adhesion deja existante.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
    $adhesionOrm = new AdhesionOrm($manager);
    $created = $adhesionOrm->createAdhesion($postData,$user);
    if(!$created)return $this->json(['message'=>'Insertion echoué'],Response::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'ok'],Response::HTTP_OK);
  }

  #[Route('/api/auth/adhesion/user/{id}', name: 'add_user_adhesion_by_user_id', methods:'POST')]
  public function addUserAdhesionByUserId(#[CurrentUser]? User $user,?User $userToEdit, Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits insufisants'],Response::HTTP_FORBIDDEN);
    if(!$userToEdit)return $this->json(['message'=>'utilisateur introuvable'],Response::HTTP_OK);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->adhesionValidator->validateAdhesion($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $adhesion = $manager->getRepository(Adhesion::class)->findOneBy(['user'=>$userToEdit]);
		if($adhesion) return $this->json(['message'=>'adhesion deja existante.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
    $adhesionOrm = new AdhesionOrm($manager);
    $created = $adhesionOrm->createAdhesion($postData,$userToEdit);
    if(!$created)return $this->json(['message'=>'Insertion echoué'],Response::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'ok'],Response::HTTP_OK);
  }

  #[Route('/api/auth/adhesion/{id}', name: 'edit_user_adhesion_by_id', methods:'POST')]
  public function editUserAdhesionById(#[CurrentUser]? User $user,?Adhesion $adhesion,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits insufisant'],Response::HTTP_FORBIDDEN);
		if(!$adhesion) return $this->json(['message'=>'adhesion non trouvable.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->adhesionValidator->validateAdhesion($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $adhesionOrm = new AdhesionOrm($manager);
    $edited = $adhesionOrm->editAdhesion($adhesion,$postData);
    if(!$edited)return $this->json(['message'=>'Modification echoué'],Response::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'ok'],Response::HTTP_OK);
  }

  #[Route('/api/auth/adhesion/user/{id}', name: 'edit_user_adhesion_user_by_id', methods:'POST')]
  public function editUserAdhesionByUserId(#[CurrentUser]? User $user,?User $userToEdit,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits insufisant'],Response::HTTP_FORBIDDEN);
		$adhesion = $manager->getRepository(Adhesion::class)->findOneBy(['user'=>$userToEdit]);
    if(!$adhesion) return $this->json(['message'=>'adhesion non trouvable.'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR); 
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->adhesionValidator->validateAdhesion($postData);
    if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $adhesionOrm = new AdhesionOrm($manager);
    $edited = $adhesionOrm->editAdhesion($adhesion,$postData);
    if(!$edited)return $this->json(['message'=>'Modification echoué'],Response::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'ok'],Response::HTTP_OK);
  }


}
