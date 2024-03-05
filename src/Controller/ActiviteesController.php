<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ActiviteesController extends AbstractController
{
    // #[Route('/activitees', name: 'app_activitees')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/ActiviteesController.php',
    //     ]);
    // }
    #[Route('/api/auth/activitees', name: 'get_all_activitees')]
    public function getAllActivitees(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ActiviteesController.php',
        ]);
    }
}
