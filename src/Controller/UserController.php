<?php

namespace App\Controller;

use App\Entity\Pairs;
use App\Entity\Swipes;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    protected $entityManager;
    public function __construct(ManagerRegistry $doctrine) {
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/swipe', name: 'swipe')]
    public function swipe(Request $request): Response
    {

        $userAId = $request->get('userA');
        $userBId = $request->get('userB');
        $action = $request->get('action');

        $userA = $this->entityManager->getRepository(User::class)->find($userAId);
        $userB = $this->entityManager->getRepository(User::class)->find($userBId);

        if ($userA && $userB && ($action == "0" || $action == "1")) {

            $swipe = new Swipes();

            $swipe->setUserA($userA);
            $swipe->setUserB($userB);
            $swipe->setAction($action);
            $this->entityManager->persist($swipe);

            // Check if swipe is pair

            if ($swipe->isAction()) {

                foreach ($userB->getSwipes() as $swipeB) {

                    if ($swipeB->getUserB()->getId() == $userA->getId() && $swipeB->isAction()) {

                        $pairA = new Pairs();
                        $pairA->setUserA($userA);
                        $pairA->setUserB($userB);
                        $this->entityManager->persist($pairA);

                        $pairB = new Pairs();
                        $pairB->setUserA($userB);
                        $pairB->setUserB($userA);
                        $this->entityManager->persist($pairB);
                    }
                }
            }
        }

        $this->entityManager->flush();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/generate/users', name: 'generateUsers')]
    public function generateUsers(): Response
    {
        $faker = Faker\Factory::create();
        $users = [];

        for ($i = 0; $i <= 10; $i++) {

            $name = $faker->userName;
            $users[] = $name;

            $user = new User();
            $user->setName($name);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return $this->render('user/generated.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('user/{idUser}/getPairs', name: 'getPairs', requirements: ['idUser' => '\d+'])]
    public function getPairs(int $idUser): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($idUser);

        $pairs = $user->getPairs();

        $temp = [];
        foreach($pairs as $pair) {
            $temp[] = $pair->getUserA()->getName() . ' + ' . $pair->getUserB()->getName();
        }

        return $this->render('user/getPairs.html.twig', [
            'userName'  => $user->getName(),
            'pairs' => $temp,
        ]);
    }
}
