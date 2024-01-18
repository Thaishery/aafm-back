<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class AppFixturesTest extends TestCase
{
    private $objectManager;
    private $appFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->appFixtures = new AppFixtures();
    }

    public function testLoad()
    {
        // Mock the ObjectRepository
        $objectRepository = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
        $objectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);  // Assuming there is no existing user

        // Mock the getRepository method to return the mocked ObjectRepository
        $this->objectManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($objectRepository);

        // Call the load method with the mocked ObjectManager
        $this->appFixtures->load($this->objectManager);
    }
}
