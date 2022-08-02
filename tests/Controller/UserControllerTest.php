<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Pairs;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\HttpClient;

class UserControllerTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testUsersCreatePairs()
    {
        $userA = new User();
        $userA->setName('UserTestA');
        $this->entityManager->persist($userA);

        $userB = new User();
        $userB->setName('UserTestB');
        $this->entityManager->persist($userB);

        $this->entityManager->flush();

        $client = HttpClient::create();
        $client->request('POST', 'http://127.0.0.1:8000/swipe', [
            'body' => [
                'userA' => $userA->getId(),
                'userB' => $userB->getId(),
                'action' => '1'
            ]
        ]);

        $client->request('POST', 'http://127.0.0.1:8000/swipe', [
            'body' => [
                'userA' => $userB->getId(),
                'userB' => $userA->getId(),
                'action' => '1'
            ]
        ]);

        $userTest = $this->entityManager->getRepository(User::class)->find($userA->getId());

        $shouldBe1 = $userTest->getPairs();

        $this->assertEquals(1, count($shouldBe1));
    }
}
