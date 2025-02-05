<?php

namespace App\Controller\Customer;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_customer_task_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupère uniquement les tâches du customer connecté
        $tasks = $entityManager->getRepository(Task::class)->findBy([
            'owner' => $this->getUser(),
        ]);

        return $this->render('customer/task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Vérification que la tâche appartient bien au customer connecté
        if ($task->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres tâches.');
            return $this->redirectToRoute('app_default');
        }

        $form = $this->createForm(TaskType::class, $task, [
            // Vous pouvez passer des options spécifiques si nécessaire
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si nécessaire, gérer la mise à jour de "updatedAt", le traitement des images, etc.
            $task->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Tâche mise à jour avec succès !');
            return $this->redirectToRoute('app_default');
        }

        return $this->render('customer/task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_customer_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($task->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres tâches.');
            return $this->redirectToRoute('app_default');
        }

        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche supprimée avec succès.');
        }

        return $this->redirectToRoute('app_default');
    }
}
