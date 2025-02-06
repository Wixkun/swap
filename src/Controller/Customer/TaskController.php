<?php

namespace App\Controller\Customer;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        if ($task->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres tâches.');
            return $this->redirectToRoute('app_default');
        }

        $form = $this->createForm(TaskType::class, $task, [
            'existing_images' => $task->getImagePaths() ?? [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTimeImmutable());

            $removeImages = $form->get('removeImages')->getData();
            if (!empty($removeImages)) {
                $existingPaths = $task->getImagePaths();
                foreach ($removeImages as $filenameToRemove) {
                    if (($key = array_search($filenameToRemove, $existingPaths)) !== false) {
                        unset($existingPaths[$key]);
                        $filePath = $this->getParameter('uploads_directory') . '/' . $filenameToRemove;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                $task->setImagePaths(array_values($existingPaths));
            }
            $this->handleImageUpload($form, $task);

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

    private function handleImageUpload($form, Task $task): void
    {
        /** @var UploadedFile[] $uploadedFiles */
        $uploadedFiles = $form->get('imageFiles')->getData();

        if (!empty($uploadedFiles)) {
            $existingPaths = $task->getImagePaths() ?? [];

            foreach ($uploadedFiles as $imageFile) {
                if ($imageFile instanceof UploadedFile) { // Vérifie que c'est un fichier
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Impossible d\'uploader l\'image : ' . $e->getMessage());
                        continue;
                    }

                    $existingPaths[] = $newFilename;
                }
            }

            $task->setImagePaths($existingPaths);
        }
    }
}
