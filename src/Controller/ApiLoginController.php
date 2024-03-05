<?php

namespace App\Controller;

//internal symfony :
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Psr\Log\LoggerInterface;

//HTTP :
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//User related : 
use App\Entity\User;
use App\Service\GoogleOAuth2\GetToken;
use App\Service\User\UserValidator;
use App\Service\User\UserInternalCreator;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // Import JWTTokenManagerInterface

//database persistance : 
use Doctrine\ORM\EntityManagerInterface;

class ApiLoginController extends AbstractController
{

  public function __construct(
    private LoggerInterface $logger,
) {
}

  #[Route('/api/users/internal/login', name: 'api_login')]
  public function login(#[CurrentUser] ? User $user,JWTTokenManagerInterface $jwtManager): Response
  {
    if (null === $user) {
      return $this->json([
          'message' => 'missing credentials',
      ], Response::HTTP_UNAUTHORIZED);
    }

    $token = $jwtManager->create($user);

    return $this->json([
        'user'  => $user->getUserIdentifier(),
        'token' => $token,
    ]);
  }

  #[Route('/api/users/external/login/', name: 'api_external_login',methods:'GET')]
  public function externalLogin(Request $req)
  {
    $error= $req->query->get('error');
    if(isset($error)&&!empty($error))return $this->json(['message'=>$error],Response::HTTP_UNAUTHORIZED);
    $code = $req->query->get('code');
    $response = [
      'code'=>$code,
    ];
    $token = new GetToken($response);
    $token = $token->getToken();
    if(!$token) return $this->json(['message'=>'erreur lors de la récupération du token'],Response::HTTP_ACCEPTED);

    //! there we have an refresh token, and a token, let's find out if we have to create an account or no :thinking:
    //! we will have to bind the token to the user too ... 

    return $this->json(['message'=>$token],Response::HTTP_ACCEPTED);
  }
  #[Route('/api/users/internal/register', name:'api_register', methods:'POST')]
  public function registerUser(Request $request, UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager, UserInternalCreator $userCreator): Response
  {
    //vérifie que l'on as bien un post : 
    $postData = json_decode($request->getContent(), false);
    // var_dump($postData);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], Response::HTTP_FORBIDDEN); 
    
    //vérifie les donnée utilisateurs : 
    $userValidator = new UserValidator;
    $verifiUserDataCreate = $userValidator->verifiUserDataCreate($postData); 
    if($verifiUserDataCreate['isValid'] == false) return $this->json($verifiUserDataCreate['messages'], Response::HTTP_FORBIDDEN);
    
    //créer l'utilisateur : 
    //todo : gestion erreur email already in use... (gérer mais changer le message d'erreurs lors du cas )
    //todo : adaptation code necessaire. 
    $user = $userCreator->createInternalUser($postData, $passwordHasher, $manager);
    if(!$user) return $this->json(['message' => 'Erreur lors de la création de l\'utilisateur'],Response::HTTP_INTERNAL_SERVER_ERROR);
    
    //réponse ok (utilisateur créer) : 
    return $this->json(
      $postData
    , Response::HTTP_OK);
  }

}
