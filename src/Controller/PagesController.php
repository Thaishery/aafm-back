<?php

namespace App\Controller;

use App\Entity\Pages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    
    #[Route('/', name: 'index')]
    public function index(): JsonResponse
    {
        return $this->json(['message' => 'I am woriking'],JsonResponse::HTTP_OK);
    }

    #[Route('/api/public/get_home_content', name: 'get_home_content')]
    public function getHomeContent(EntityManagerInterface $manager): JsonResponse
    {
        $pages = $manager->getRepository(Pages::class)->findOneBy(['name'=>'home']);
        if(empty($pages))return $this->json(['message'=>'Erreur lors du chargement des donées'],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        if($pages instanceof Pages) $results= $pages->populate();
        return $this->json(['content'=>$results],JsonResponse::HTTP_OK);
    }
}
