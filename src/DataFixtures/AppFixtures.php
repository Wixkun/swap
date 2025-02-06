<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Skill;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\TaskProposal;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $adminUser = new User();
        $adminUser->setEmail('admin@gmail.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword(
            $this->passwordHasher->hashPassword($adminUser, 'admin12345678')
        );
        $manager->persist($adminUser);

        $customerUser = new User();
        $customerUser->setEmail('customer@gmail.com');
        $customerUser->setRoles(['ROLE_CUSTOMER']);
        $customerUser->setPassword(
            $this->passwordHasher->hashPassword($customerUser, 'customer12345678')
        );
        $customer = new Customer();
        $customer->setFirstName($faker->firstName);
        $customer->setLastName($faker->lastName);
        $customer->setCity($faker->city);

        $customer->setIdUser($customerUser);
        $customerUser->setIdCustomer($customer);

        $manager->persist($customer);
        $manager->persist($customerUser);

        $agentUser = new User();
        $agentUser->setEmail('agent@agent.agent');
        $agentUser->setRoles(['ROLE_AGENT']);
        $agentUser->setPassword(
            $this->passwordHasher->hashPassword($agentUser, 'agent12345678')
        );
        $agent = new Agent();
        $agent->setPseudo($faker->userName);
        $agent->setPhoneNumber($faker->phoneNumber);
        $agent->setRatingGlobal($faker->randomFloat(2, 0, 5));

        $agent->setIdUser($agentUser);
        $agentUser->setIdAgent($agent);

        $manager->persist($agent);
        $manager->persist($agentUser);

        $genericUsers      = [];
        $genericCustomers  = [];
        $genericAgents     = [];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password')
            );

            if ($i < 5) {
                $user->setRoles(['ROLE_CUSTOMER']);

                $cust = new Customer();
                $cust->setFirstName($faker->firstName);
                $cust->setLastName($faker->lastName);
                $cust->setCity($faker->city);

                $cust->setIdUser($user);
                $user->setIdCustomer($cust);

                $manager->persist($cust);
                $genericCustomers[] = $cust;
            } else {
                $user->setRoles(['ROLE_AGENT']);

                $ag = new Agent();
                $ag->setPseudo($faker->userName);
                $ag->setPhoneNumber($faker->phoneNumber);
                $ag->setRatingGlobal($faker->randomFloat(2, 0, 5));

                $ag->setIdUser($user);
                $user->setIdAgent($ag);

                $manager->persist($ag);
                $genericAgents[] = $ag;
            }

            $manager->persist($user);
            $genericUsers[] = $user;
        }

        $skillData = [
            ['name' => 'Bagarre',       'description' => 'Gestion et résolution de conflits physiques ou verbaux'],
            ['name' => 'Rupture',       'description' => 'Accompagnement et médiation lors de séparations difficiles'],
            ['name' => 'File d’attente','description' => 'Organisation et gestion des flux de personnes'],
            ['name' => 'Démission',     'description' => 'Conseils et démarches administratives pour une démission'],
            ['name' => 'Manifestation', 'description' => 'Supervision et encadrement de mouvements de protestation']
        ];

        $skills = [];
        foreach ($skillData as $data) {
            $skill = new Skill();
            $skill->setName($data['name']);
            $skill->setDescription($data['description']);

            $manager->persist($skill);
            $skills[] = $skill;
        }

        $allAgents = array_merge([$agent], $genericAgents);

        foreach ($allAgents as $agentObj) {
            $nbSkills = rand(1, 3);
            $selectedSkills = $faker->randomElements($skills, $nbSkills);
            foreach ($selectedSkills as $skill) {
                $skill->addIdAgent($agentObj);
            }
        }

        $tagNames = ['Violence', 'Séparation', 'Organisation', 'Procédure', 'Conflit'];
        $tags = [];
        foreach ($tagNames as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

    $possibleTasks = [
        [
            'title'       => 'Résoudre une bagarre devant le bar local',
            'description' => 'Besoin d’un agent pour calmer la situation et éviter les débordements.'
        ],
        [
            'title'       => 'Gérer la rupture d’un couple en litige',
            'description' => 'Cherche médiateur pour gérer documents, discussions, et partager les biens.'
        ],
        [
            'title'       => 'Organisation d’une file d’attente pour la billetterie',
            'description' => 'Nécessite un professionnel pour mettre en place des barrières et gérer le flux de personnes.'
        ],
        [
            'title'       => 'Accompagnement à la démission d’un employé',
            'description' => 'Besoin de conseils pour rédiger les courriers et faire respecter les délais légaux.'
        ],
        [
            'title'       => 'Sécuriser une manifestation pacifique',
            'description' => 'Besoin d’une équipe pour superviser le cortège et éviter les débordements.'
        ],
    ];

    $allCustomers = array_merge([$customer], $genericCustomers);

    $tasks = [];
    foreach ($possibleTasks as $taskData) {
        $randomOwner = $faker->randomElement($allCustomers);

        $task = new Task();
        $task->setTitle($taskData['title']);
        $task->setDescription($taskData['description']);
        $task->setStatus('pending');
        $task->setImagePaths([$faker->imageUrl()]);
        $task->setOwner($randomOwner->getIdUser());
        $task->addTag($faker->randomElement($tags));
        $task->setUpdatedAt(new \DateTime());

        $manager->persist($task);
        $tasks[] = $task;
    }

    $manager->flush();

        foreach ($allAgents as $agentObj) {
            $review = new Review();
            $review->setIdAgent($agentObj);
            $review->setRating($faker->numberBetween(1, 5));
            $review->setComment($faker->paragraph);
            $review->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($review);
        }

        $manager->flush();
    }
}