<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends ApiTestCase
{

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    use ReloadDatabaseTrait;

    public function testLogin(): void
    {

        $client = self::createClient();
        $container = self::getContainer();

        $user = $this->getContainer()->get(UserFactory::createOne());
        dump($user);

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();



        // retrieve a token
        $response = $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@test.com',
                'password' => 'test',
            ],
        ]);
        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/articles');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/articles', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }

} 