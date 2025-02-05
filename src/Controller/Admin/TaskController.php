<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('admin/task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setOwner($this->getUser());

            $this->handleImageUpload($form, $task);

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('admin/task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($task->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres tâches.');
            return $this->redirectToRoute('app_task_index');
        }

        // Passez les images existantes dans l'option "existing_images"
        $form = $this->createForm(TaskType::class, $task, [
            'existing_images' => $task->getImagePaths() ?? [],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTimeImmutable());

            // Traitement du champ pour la suppression d'images existantes
            $removeImages = $form->get('removeImages')->getData();
            if (!empty($removeImages)) {
                $existingPaths = $task->getImagePaths();
                foreach ($removeImages as $filenameToRemove) {
                    if (($key = array_search($filenameToRemove, $existingPaths)) !== false) {
                        unset($existingPaths[$key]);
                        // Optionnel : Supprimez le fichier du système de fichiers
                        $filePath = $this->getParameter('uploads_directory') . '/' . $filenameToRemove;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                // Ré-indexation du tableau et mise à jour de l'entité
                $task->setImagePaths(array_values($existingPaths));
            }

            // Traitement de l'ajout de nouvelles images
            $this->handleImageUpload($form, $task);

            $entityManager->flush();

            $this->addFlash('success', 'Tâche mise à jour avec succès !');
            return $this->redirectToRoute('app_default');
        }

        return $this->render('admin/task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('admin/task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($task->getOwner() !== $user && !$this->isGranted('ROLE_ADMIN')) {
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
