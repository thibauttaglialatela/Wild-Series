<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/actor")
 */
class ActorController extends AbstractController
{
    /**
     * show all rows from Actor's entity
     *
     * @Route("/", name="actor_index")
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
     * @Route("/new", name="actor_new")
     */

    public function new(Request $request): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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
     * @Route("/{id}", name="actor_show", methods={"GET"})
     */
    public function show(Actor $actor): Response
    {
        $actorPrograms = $actor->getPrograms();

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'actorPrograms' => $actorPrograms,
        ]);
    }

}
