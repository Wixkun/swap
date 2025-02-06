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
        $adminUser->setEmail('admin@admin.admin');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword(
            $this->passwordHasher->hashPassword($adminUser, 'admin12345678')
        );
        $manager->persist($adminUser);

        $customerUser = new User();
        $customerUser->setEmail('customer@customer.customer');
        $customerUser->setRoles(['ROLE_CUSTOMER']);
        $customerUser->setPassword(
            $this->passwordHasher->hashPassword($customerUser, 'customer12345678')
        );
        $customer = new Customer();
        $customer->setFirstName($faker->firstName);
        $customer->setLastName($faker->lastName);
        $customer->setCity($faker->city);
        // Liaison bidirectionnelle
        $customer->setIdUser($customerUser);
        $customerUser->setIdCustomer($customer);

        $manager->persist($customer);
        $manager->persist($customerUser);

        $agentUser = new User();
        $agentUser->setEmail('agent@agent.agent');
        // Selon votre demande, cet utilisateur a le rôle ROLE_CUSTOMER (modifiez si besoin)
        $agentUser->setRoles(['ROLE_CUSTOMER']);
        $agentUser->setPassword(
            $this->passwordHasher->hashPassword($agentUser, 'agent12345678')
        );
        $agent = new Agent();
        $agent->setPseudo($faker->userName);
        $agent->setPhoneNumber($faker->phoneNumber);
        $agent->setRatingGlobal($faker->randomFloat(2, 0, 5));
        // Liaison bidirectionnelle
        $agent->setIdUser($agentUser);
        $agentUser->setIdAgent($agent);

        $manager->persist($agent);
        $manager->persist($agentUser);

        $genericUsers   = [];
        $genericCustomers = [];
        $genericAgents    = [];

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

        $skills = [];
        for ($i = 0; $i < 5; $i++) {
            $skill = new Skill();
            $skill->setName(ucfirst($faker->word));
            $skill->setDescription($faker->sentence);
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

        $tags = [];
        for ($i = 0; $i < 5; $i++) {
            $tag = new Tag();
            $tag->setName(ucfirst($faker->word));
            $manager->persist($tag);
            $tags[] = $tag;
        }

        $allCustomers = array_merge([$customer], $genericCustomers);
        $tasks = [];
        foreach ($allCustomers as $cust) {
            $numTasks = rand(1, 2);
            for ($i = 0; $i < $numTasks; $i++) {
                $task = new Task();
                $task->setTitle($faker->sentence(6, true));
                $task->setDescription($faker->paragraph);
                $task->setImagePaths([$faker->imageUrl()]);
                $task->setStatus('pending');
                $task->setOwner($cust->getIdUser());
                $task->addTag($faker->randomElement($tags));
                $task->setUpdatedAt(new \DateTime());

                $manager->persist($task);
                $tasks[] = $task;
            }
        }

        foreach ($tasks as $task) {
            $proposal = new TaskProposal();
            $proposal->setTask($task);
            $proposal->setAgent($faker->randomElement($allAgents));
            $proposal->setProposedPrice($faker->randomFloat(2, 50, 500));
            $proposal->setStatus('pending');
            $manager->persist($proposal);
        }

        $conversations = [];
        foreach ($allCustomers as $cust) {
            $conversation = new Conversation();
            $conversation->setIdCustomer($cust);
            $conversation->setIdAgent($faker->randomElement($allAgents));
            $conversation->setStartedAt(new \DateTimeImmutable());
            $manager->persist($conversation);
            $conversations[] = $conversation;
        }

        foreach ($conversations as $conv) {
            for ($i = 0; $i < 3; $i++) {
                $message = new Message();
                $message->setContent($faker->sentence);
                $message->setSentAt(new \DateTimeImmutable());
                $message->setIdConversation($conv);
                if ($i % 2 === 0) {
                    $message->setIdUser($conv->getIdCustomer()->getIdUser());
                } else {
                    $message->setIdUser($conv->getIdAgent()->getIdUser());
                }
                $manager->persist($message);
            }
        }

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
