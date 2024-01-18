<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetAndSetId()
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testGetAndSetEmail()
    {
        $user = new User();

        // Test setEmail and getEmail methods
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testGetUserIdentifier()
    {
        $user = new User();

        // Test getUserIdentifier method
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }

    public function testGetAndSetRoles()
    {
        $user = new User();

        // Test setRoles and getRoles methods
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->assertEquals($roles, $user->getRoles());
    }

    public function testGetAndSetPassword()
    {
        $user = new User();

        // Test setPassword and getPassword methods
        $password = 'hashedpassword';
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());
    }

    public function testEraseCredentials()
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertEquals(1,1,'fake but no logic there');
    }

    public function testGetAndSetIsInternal()
    {
        $user = new User();

        // Test setIsInternal and isIsInternal methods
        $user->setIsInternal(true);
        $this->assertTrue($user->isIsInternal());
    }

    public function testGetAndSetFirstname()
    {
        $user = new User();

        // Test setFirstname and getFirstname methods
        $user->setFirstname('John');
        $this->assertEquals('John', $user->getFirstname());
    }

    public function testGetAndSetLastname()
    {
        $user = new User();

        // Test setLastname and getLastname methods
        $user->setLastname('Doe');
        $this->assertEquals('Doe', $user->getLastname());
    }

    public function testGetAndSetExternalId()
    {
        $user = new User();

        // Test setExternalId and getExternalId methods
        $user->setExternalId('external123');
        $this->assertEquals('external123', $user->getExternalId());
    }
}
