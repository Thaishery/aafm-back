<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    // Check if the user with the email already exists
    $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $this->getFakeEmail()]);
    if (!$existingUser) {
        $user = new User();
        $user->setEmail($this->getFakeEmail());
        $user->setPassword('p4$sW0rD1');
        $user->setIsInternal(1);
        $manager->persist($user);
        $manager->flush();
    }
  }
  public function getFakeEmail():string{
    return 'alreadyExist@gdeb.fr';
  }
}
