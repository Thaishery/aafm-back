<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/auth/test', name: 'app_test')]
    public function test(#[CurrentUser] ? User $user): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller! '. $user->getEmail(),
            'path' => 'src/Controller/TestController.php',
        ]);
    }
    #[Route('/api/auth/test/admin', name: 'app_test_admin')]
    public function testAdmin(#[CurrentUser] ? User $user): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller! '. $user->getEmail(),
            'path' => 'src/Controller/TestController.php',
        ]);
    }
}
