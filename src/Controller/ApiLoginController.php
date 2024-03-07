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
use App\Service\GoogleOAuth2\GetUserInfos;
use App\Service\User\UserExternalManager;
use App\Service\User\UserValidator;
use App\Service\User\UserInternalCreator;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // Import JWTTokenManagerInterface

//database persistance : 
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiLoginController extends AbstractController
{

  public function __construct( private LoggerInterface $logger, private JWTEncoderInterface $jwtEncoder) 
  {
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

  #[Route('/api/users/internal/validateToken', name: 'validate_token', methods:'POST')]
  public function validateToken(Request $req,EntityManagerInterface $manager,JWTTokenManagerInterface $jwtManager): Response
  {
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], Response::HTTP_FORBIDDEN);
    if(!isset($postData->token)) return $this->json(['message'=>'token Ivalide'],Response::HTTP_FORBIDDEN);
    $valid = true;
    try {
      $payload = $this->jwtEncoder->decode($postData->token);
    } catch (JWTDecodeFailureException $ex) {
      $valid = false;
    }
    if(!$valid)return $this->json(['message'=>'token Ivalide'],Response::HTTP_FORBIDDEN);
    if(!isset($payload["username"]))return $this->json(['message'=>'token Ivalide'],Response::HTTP_FORBIDDEN);
    $user = $manager->getRepository(User::class)->findOneBy(['email'=>$payload['username']]);
    if(!$user) return $this->json(['message'=>'token Ivalide'],Response::HTTP_FORBIDDEN);
    $token = $jwtManager->create($user);

    return $this->json([
        'user'  => $user->getUserIdentifier(),
        'token' => $token,
    ]);
  }

  #[Route('/api/users/internal/googlelogin/{uuid}', name: 'api_google_login')]
  public function googleLogin(string $uuid,EntityManagerInterface $manager,JWTTokenManagerInterface $jwtManager): Response
  {
    $userManager = new UserExternalManager();
    $response = $userManager->logExternalUser($uuid,$manager,$jwtManager);
    return $this->json($response,Response::HTTP_OK);
  }

  #[Route('/api/users/external/login/', name: 'api_external_login',methods:'GET')]
  public function externalLogin(Request $req,UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager,JWTTokenManagerInterface $jwtManager)
  {
    $error= $req->query->get('error');
    if(isset($error)&&!empty($error))return $this->json(['message'=>$error],Response::HTTP_UNAUTHORIZED);
    $code = $req->query->get('code');
    $response = [
      'code'=>$code,
    ];
    $token = new GetToken($response);
    $token = json_decode($token->getToken(),false);
    if(!$token) return $this->json(['message'=>'erreur lors de la récupération du token'],Response::HTTP_ACCEPTED);
    $userInfos = new GetUserInfos($token);
    $userInfos = $userInfos->getUserInfos();
    if(!$userInfos)return $this->json(['message'=>'Imposible de décoder le JWT GOOGLE'],Response::HTTP_ACCEPTED);
    //? on as les infos, on les envoi a notre manager : 
    $userManager = new UserExternalManager();
    $response = $userManager->createOrPrepareExternalUser($userInfos,$passwordHasher,$manager,$jwtManager);
    return $this->redirect($_ENV['CLIENT_URL'].'/googleauth/'.$response);
  }
  
  #[Route('/api/users/internal/register', name:'api_register', methods:'POST')]
  public function registerUser(Request $request, UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $manager, UserInternalCreator $userCreator,JWTTokenManagerInterface $jwtManager): Response
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
    
    $token = $jwtManager->create($user);

    return $this->json([
        'user'  => $user->getUserIdentifier(),
        'token' => $token,
    ]);
    //réponse ok (utilisateur créer) : 
    return $this->json(
      $postData
    , Response::HTTP_OK);
  }

}
