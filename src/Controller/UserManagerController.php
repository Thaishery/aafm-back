<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserManagerController extends AbstractController
{
  #[Route('/api/users/internal/edituser', name: 'app_user_manager', methods:'POST')]
  public function index(#[CurrentUser] ? User $user, Request $req): JsonResponse
  {
    //vérifier user. 
    //? admin ou utilisateur actuel ? 
    $isValidUser = 0; 
    //KO : 
    if(!$isValidUser) return $this->json(['message'=>'Action Imposible'],JsonResponse::HTTP_FORBIDDEN);
    //ok : 
        //update user : 
        //send response
    return $this->json(['message' => 'User updated'],JsonResponse::HTTP_OK);
  }

}