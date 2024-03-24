<?php

namespace App\Controller;

use App\Entity\Activitees;
use App\Entity\User;
use App\Service\Activitees\ActiviteesOrm;
use App\Service\Activitees\ActiviteesValidator;
use App\Service\User\UserVerifiRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ActiviteesController extends AbstractController
{
  private $roleChecker;
  private $activiteesValidator;
  public function __construct()
  {
    $this->roleChecker = new UserVerifiRole();
    $this->activiteesValidator = new ActiviteesValidator();
  }
  
  /**
   * Récupére les activité d'un utilisateur connecter 
   * @param User $user utilisateur actuel
   * @param EntityManagerInterface $manager ORM repository
   * @return JsonResponse 
   * @author gdeb@gdeb.fr
   */
  #[Route('/api/auth/activitees', name: 'get_all_activitees', methods:'GET')]
  public function getAllActivitees(#[CurrentUser]? User $user,EntityManagerInterface $manager): JsonResponse
  {
    $activitees = $manager->getRepository(Activitees::class)->findAll();
    $result = [];
    foreach($activitees as $activite){
      //? la ligne suivante est pour mon linter : 
      /** @var Activitees $activite */
      $result[] = $activite->populate($user);
    }
    if(empty($result)) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    return $this->json(['message'=>$result],Response::HTTP_OK);
  }

  #[Route('/api/auth/activitees_users/{id}', name: 'get_activitees_user', methods:'GET')]
  public function getActiviteesUsers(#[CurrentUser]? User $user,?Activitees $activite,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits Insuffisants'],Response::HTTP_FORBIDDEN);
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    $users = $activite->getUser();
    $result = [];
    foreach ($users as $user){
      $result[] = [
        "id"=>$user->getId(),
        "email"=>$user->getEmail(),
        "firstname"=>$user->getFirstname(),
        "lastname"=>$user->getLastname()
      ];
    }
    return $this->json(['users'=>$result]);
  }

  #[Route('/api/auth/activitees', name: 'create_activitees', methods:'POST')]
  public function createActivitees(#[CurrentUser]? User $user,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits Insuffisants'],Response::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->activiteesValidator->validateActivitees($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $activiteesOrm = new ActiviteesOrm($manager);
    $created = $activiteesOrm->createActivitees($postData);
		if(!$created) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }
  
  #[Route('/api/auth/activitees/{id}', name: 'edit_activitees', methods:'PUT')]
  public function editActivitees(#[CurrentUser]? User $user,?Activitees $activite,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite)return $this->json(['message'=>'Activité non trouvé'],Response::HTTP_NOT_FOUND);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits Insuffisants'],Response::HTTP_FORBIDDEN);
    $postData = json_decode($req->getContent(), false);
    if(!$postData||empty($postData)) return $this->json(['message' =>'Données invalide'], JsonResponse::HTTP_FORBIDDEN);
		$isValid = $this->activiteesValidator->validateActivitees($postData);
		if($isValid['isValid'] == false) return $this->json($isValid['messages'], JsonResponse::HTTP_FORBIDDEN);
    $activiteesOrm = new ActiviteesOrm($manager);
    $edited = $activiteesOrm->editActivitees($postData,$activite);
		if(!$edited) return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }
  
  #[Route('/api/auth/activitees/{id}', name: 'delete_activitees', methods:'DELETE')]
  public function deleteActivitees(#[CurrentUser]? User $user,?Activitees $activite,Request $req,EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite)return $this->json(['message'=>'Activité non trouvé'],Response::HTTP_NOT_FOUND);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user))return $this->json(['message'=>'Droits Insuffisants'],Response::HTTP_FORBIDDEN);
    $activiteesOrm = new ActiviteesOrm($manager);
    $deleted = $activiteesOrm->deleteActivitees($activite);
		if(!$deleted) return $this->json(['message'=>'Erreur lors de la supression'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message' => 'OK'],JsonResponse::HTTP_OK);
  }

  #[Route('/api/auth/activitees/{id}', name: 'get_one_activitees')]
  public function getOneActivitees(#[CurrentUser]? User $user,?Activitees $activite): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    return $this->json(['message'=>$activite->populate($user)],Response::HTTP_OK);
  }
  
  #[Route('/api/auth/activitees/sinscrire/{id}', name: 'inscription_activitees',methods:'POST')]
  public function insciptionActivitees(#[CurrentUser]? User $user,?Activitees $activite,EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MEMBER',$user)) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    $activiteesOrm = new ActiviteesOrm($manager);
    $added = $activiteesOrm->addParticipant($user,$activite);
    if(!$added)return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'OK'],Response::HTTP_OK);
  }

  #[Route('/api/auth/activitees/desinscrire/{id}', name: 'desinscription_activitees',methods:'POST')]
  public function desinsciptionActivitees(#[CurrentUser]? User $user,?Activitees $activite,EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MEMBER',$user)) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    $activiteesOrm = new ActiviteesOrm($manager);
    $added = $activiteesOrm->deleteParticipant($user,$activite);
    if(!$added)return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'OK'],Response::HTTP_OK);
  }

  #[Route('/api/auth/activitees/moderer/desinscrire/{id}/{userid}', name: 'moderer_desinscription_activitees', methods: ['POST'])]
  public function modereDesinsciptionActivitees(#[CurrentUser]? User $user,Activitees $activite, int $userid , EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    $userToRemove = $manager->getRepository(User::class)->findOneBy(['id'=>$userid]);
    if(!$userToRemove)return $this->json(['message'=>'utilsiateur introuvable'],Response::HTTP_OK);
    $activiteesOrm = new ActiviteesOrm($manager);
    $added = $activiteesOrm->deleteParticipant($userToRemove,$activite);
    if(!$added)return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'OK'],Response::HTTP_OK);
  }

  #[Route('/api/auth/activitees/moderer/inscrire/{id}/{userid}', name: 'moderer_inscription_activitees', methods: ['POST'])]
  public function modereInsciptionActivitees(#[CurrentUser]? User $user,Activitees $activite, int $userid , EntityManagerInterface $manager): JsonResponse
  {
    if(!$activite) return $this->json(['message'=>'Pas d\'activitées trouver.'],Response::HTTP_OK);
    if(!$this->roleChecker->checkUserHaveRole('ROLE_MODERATOR',$user)) return $this->json(['message'=>'Droits insuffisant'],Response::HTTP_FORBIDDEN);
    $userToRemove = $manager->getRepository(User::class)->findOneBy(['id'=>$userid]);
    if(!$userToRemove)return $this->json(['message'=>'utilsiateur introuvable'],Response::HTTP_OK);
    $activiteesOrm = new ActiviteesOrm($manager);
    $added = $activiteesOrm->addParticipant($userToRemove,$activite);
    if(!$added)return $this->json(['message'=>'Erreur lors de l\'insertion en base de données'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    return $this->json(['message'=>'OK'],Response::HTTP_OK);
  }
}
