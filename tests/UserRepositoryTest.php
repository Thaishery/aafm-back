<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{  
  private $entityManager;

  protected function setUp(): void
  {
    parent::setUp();
    $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
  }

  public function testUpgradePassword()
  {
    $entityManager = $this->entityManager;
    $registry = $this->createMock(ManagerRegistry::class);
    $registry->method('getManagerForClass')->willReturn($entityManager);
    $userRepository = new UserRepository($registry);
    
    $user = new User();
    $user->setemail('test2@gdeb.fr');
    $user->setPassword('oldhashedpassword');
    $user->setIsInternal(1);
    $userRepository->upgradePassword($user, 'newhashedpassword');
    $this->assertEquals('newhashedpassword', $user->getPassword());
  }

  public function testUpgradePasswordWithUnsupportedUserException()
  {
    $entityManager = $this->createMock(EntityManager::class);
    $registry = $this->createMock(ManagerRegistry::class);
    $registry->method('getManagerForClass')->willReturn($entityManager);
    $userRepository = new UserRepository($registry);
    $unsupportedUser = $this->createMock(PasswordAuthenticatedUserInterface::class);
    $this->expectException(UnsupportedUserException::class);
    $userRepository->upgradePassword($unsupportedUser, 'newhashedpassword');
  }
}
