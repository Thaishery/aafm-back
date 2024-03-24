<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserEditorOrmUpdate;
use App\Service\User\UserEditorRoleValidator;
use App\Service\User\UserValidator;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserManagerController extends AbstractController
{
  private $roleChecker;
  public function __construct()
  {
    $this->roleChecker = new UserVerifiRole();
  }

  #[Route('/api/auth/users/getSelf', name:'user_get_self', methods:'GET')]
  public function getSelf(#[CurrentUser] ? User $user) : Response {
    return $this->json($user->populate(),Response::HTTP_OK);
  }

  #[Route('/api/auth/users/internal/edituser', name: 'app_user_manager', methods:'POST')]
  public function editUser(#[CurrentUser] ? User $user, Request $req, UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager): JsonResponse
  {
    //varifie data : 
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN); 
    
    //validate data : 
    $validator = new UserValidator;
    $verifiUserDataCreate = $validator->verifiUserDataEdit($postData); 
    if($verifiUserDataCreate['isValid'] == false) return $this->json($verifiUserDataCreate['messages'], JsonResponse::HTTP_FORBIDDEN);
    
    //vérifier user. 
    //? admin ou utilisateur actuel ? 
    $userValidator = new UserEditorRoleValidator($manager); 
    $isValidUser = $userValidator->verifiUserEditPermision($user, $postData);
    if(!$isValidUser) return $this->json(['message'=>'Action Imposible'],JsonResponse::HTTP_FORBIDDEN);
    
    //? $userValidator->verifiUserEditPermision have set this property so calling it now w/o check is fine: 
    $userToEdit = $userValidator->getUserToEdit();
    
    $userUpdater = new UserEditorOrmUpdate($manager); 
    $isUpdate = $userUpdater->updateUser($userToEdit, $postData, $passwordHasher);
    if(!$isUpdate) return $this->json(['message'=>'Une erreur d\'insertion en base de données. '],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'User updated'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/users/getAll', name:'user_get_all', methods:'GET')]
  public function getAll(#[CurrentUser] ? User $user,EntityManagerInterface $manager) : Response {
    if(!$user) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    $users = $manager->getRepository(User::class)->findAll();
    $result= [];
    foreach($users as $oneUser){
      $result[] = $oneUser->populate();
    }
    return $this->json($result,Response::HTTP_OK);
  }

}