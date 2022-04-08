<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/actor", name="actor_")
 */
class ActorController extends AbstractController
{
    /**
     * show all rows from Actor's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();

        return $this->render(
            'actor/index.html.twig',
            ['actors' => $actors]
        );
    }

    /**
     * The controller for the actor add form
     * Display the form or deal with it
     *
     * @Route("/new", name="new")
     * @IsGranted ("ROLE_CONTRIBUTOR")
     */

    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($actor);
            $entityManager->flush();
            $this->addFlash('success', 'Un nouvel acteur vient d\' être rajouté');
            return $this->redirectToRoute('actor_index');
        }
        return $this->renderForm('actor/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Actor $actor): Response
    {
        $actorPrograms = $actor->getPrograms();

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'actorPrograms' => $actorPrograms,
        ]);
    }

    /**
     * @Route ("/{id}/edit", name="edit", methods={"GET", "POST"})
     *
     * @IsGranted ("ROLE_ADMIN")
     *
     */
    public function edit(Request $request, Actor $actor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', 'Vous venez de mettre à jour un acteur');
            return $this->redirectToRoute('actor_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     * @IsGranted ("ROLE_ADMIN")
     */
    public function delete(Request $request, Actor $actor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $actor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actor);
            $entityManager->flush();
            $this->addFlash('danger', 'Vous venez de supprimer la fiche d\' un acteur.');
        }

        return $this->redirectToRoute('actor_index', [], Response::HTTP_SEE_OTHER);
    }


}
