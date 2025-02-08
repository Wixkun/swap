<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAgentController extends AbstractController
{
    #[Route('/api/agents', name: 'api_agents_list', methods: ['GET'])]
    public function getAgents(EntityManagerInterface $entityManager): JsonResponse
    {
        $agents = $entityManager->getRepository(Agent::class)->createQueryBuilder('a')
            ->select('a.id, a.pseudo, a.phoneNumber, a.ratingGlobal')
            ->orderBy('a.ratingGlobal', 'DESC') 
            ->getQuery()
            ->getResult();
    
        return $this->json($agents);
    }

    #[Route('/api/agents', name: 'api_agents_create', methods: ['POST'])]
    public function createAgent(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
    
        if (!isset($data['idUser']) || empty($data['idUser'])) {
            return new JsonResponse(['error' => 'idUser est requis'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $user = $entityManager->getRepository(User::class)->find($data['idUser']);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur introuvable'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $agent = $serializer->deserialize($jsonContent, Agent::class, 'json');
        $agent->setIdUser($user);
    
        $roles = $user->getRoles();
        if (!in_array('ROLE_AGENT', $roles)) {
            $roles[] = 'ROLE_AGENT';
            $user->setRoles($roles);
            $entityManager->persist($user); 
        }
    
        $errors = $validator->validate($agent);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        try {
            $entityManager->persist($agent);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l’enregistrement'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Agent créé avec succès et rôle mis à jour'], JsonResponse::HTTP_CREATED);
    }    
}    
