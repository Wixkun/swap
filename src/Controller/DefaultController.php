<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        TaskRepository $taskRepository,
        TagRepository $tagRepository
    ): Response {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $uploadedFiles */
            $uploadedFiles = $form->get('imageFiles')->getData();

            if ($uploadedFiles) {
                $existingPaths = $task->getImagePaths() ?? [];

                foreach ($uploadedFiles as $imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Impossible d\'uploader l\'image : '.$e->getMessage());
                        continue;
                    }

                    $existingPaths[] = $newFilename;
                }

                $task->setImagePaths($existingPaths);
            }

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('app_default');
        }

        $tags = $tagRepository->findAll();

        $tagIds = $request->query->get('tags', '');
        $tagIds = array_filter(explode(',', $tagIds));

        if (!empty($tagIds)) {
            $tasks = $taskRepository->findByMultipleTags($tagIds);
        } else {
            $tasks = $taskRepository->findOnlyPending();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('parts/home/taskShow.html.twig', [
                'tasks' => $tasks
            ]);
        }

        return $this->render('index.html.twig', [
            'form'  => $form->createView(),
            'tasks' => $tasks,
            'tags'  => $tags,
            'selectedTags' => $tagIds,
        ]);
    }
}
