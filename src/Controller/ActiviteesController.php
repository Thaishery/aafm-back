<?php

namespace App\Controller;

use App\Entity\Activitees;
use App\Entity\User;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ActiviteesController extends AbstractController
{
  private $roleChecker;
  public function __construct()
  {
    $this->roleChecker = new UserVerifiRole();
  }
  // #[Route('/activitees', name: 'app_activitees')]
  // public function index(): JsonResponse
  // {
  //     return $this->json([
  //         'message' => 'Welcome to your new controller!',
  //         'path' => 'src/Controller/ActiviteesController.php',
  //     ]);
  // }
  
  #[Route('/api/auth/activitees', name: 'get_all_activitees')]
  public function getAllActivitees(#[CurrentUser]? User $user,EntityManagerInterface $manager): JsonResponse
  {
    $activitees = $manager->getRepository(Activitees::class)->findAll();
    $result = [];
    foreach($activitees as $activite){
      // $result[] = $activite->populateToRender();
    }
    if(empty($result)) return $this->json(['message'=>'Pas d\'activitées trouver.']);
    return $this->json(['message'=>$result],Response::HTTP_OK);
  }

  #[Route('/api/auth/activitees/{id}', name: 'get_one_activitees')]
  public function getOneActivitees(#[CurrentUser]? User $user,?Activitees $activite): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.']);
    // $res = $activite->populateDetails();
    $res = $activite;
    return $this->json(['message'=>$res],Response::HTTP_OK);
  }
  
  #[Route('/api/auth/activitees/sinscrire/{id}', name: 'inscription_activitees')]
  public function insciptionActivitees(#[CurrentUser]? User $user,?Activitees $activite): JsonResponse
  {
    $this->roleChecker->checkUserHaveRole('ROLE_MEMBER',$user);
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.']);
    // $res = $activite->populateDetails();
    $res = $activite;
    return $this->json(['message'=>$res],Response::HTTP_OK);
  }
  
}
