<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Nouveau Task
        $task = new Task();

        // Créer le formulaire
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        // Si soumis + valide
        if ($form->isSubmitted() && $form->isValid()) {

            // -- 1) Récupération du fichier uploadé
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            // -- 2) S'il existe, on le déplace dans /public/uploads/tasks/
            if ($imageFile) {
                // On crée un nouveau nom unique, ex : "63f5ed2f4cc73.jpg"
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'), // On verra plus bas ce paramètre
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur (permissions, disque plein, etc.)
                    $this->addFlash('error', 'Impossible d\'uploader l\'image');
                }

                // On stocke le nom du fichier en BDD
                $task->setImagePath($newFilename);
            }

            // -- 3) Sauvegarde en BDD
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('app_default'); // Page d'accueil ou autre
        }

        return $this->render('parts/home/taskForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

