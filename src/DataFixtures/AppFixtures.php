<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //ensure we already have an user to test against :  
        $user = new User();
        $user->setEmail('alreadyExist@gdeb.fr');
        $user->setPassword('p4$sW0rD1');
        $user->setIsInternal(1);

        // $product = new Product();
        $manager->persist($user);

        $manager->flush();
    }
}
