<?php

namespace App\Controller;

//internal symfony :
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;

//HTTP :
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//User related : 
use App\Entity\User;
use App\Service\UserValidator;
use App\Service\UserInternalCreator;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//database persistance : 
use Doctrine\ORM\EntityManagerInterface;

class ApiLoginController extends AbstractController
{
  #[Route('/api/users/internal/login', name: 'api_login')]
  // public function index(): JsonResponse
  public function index(#[CurrentUser] ? User $user): Response
  {
    if (null === $user) {
      return $this->json([
          'message' => 'missing credentials',
      ], Response::HTTP_UNAUTHORIZED);
    }
    $token = "ok"; // somehow create an API token for $user
    return $this->json([
        'user'  => $user->getUserIdentifier(),
        'token' => $token,
    ]);
  }
  #[Route('/api/users/internal/register', name:'api_register', methods:'POST')]
  public function registerUser(Request $request, UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager, UserInternalCreator $userCreator): Response
  {
    //vérifie que l'on as bien un post : 
    $postData = json_decode($request->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], Response::HTTP_FORBIDDEN); 
    
    //vérifie les donnée utilisateurs : 
    $userValidator = new UserValidator;
    $verifiUserData = $userValidator->verifiUserData($postData); 
    if($verifiUserData['isValid'] == false) return $this->json($verifiUserData['messages'], Response::HTTP_FORBIDDEN);
    
    //créer l'utilisateur : 
    $user = $userCreator->createInternalUser($postData, $passwordHasher, $manager);
    if(!$user) return $this->json(['message' => 'Erreur lors de la création de l\'utilisateur'],Response::HTTP_INTERNAL_SERVER_ERROR);
    
    //réponse ok (utilisateur créer) : 
    return $this->json(
      $postData
    , Response::HTTP_OK);
  }
  
}
