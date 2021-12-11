<?php

namespace App\Controller;

use App\Entity\Actor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/actor")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/{id}", name="actor_show", methods={"GET"})
     */
    public function show(Actor $actor): Response
    {
        $actorPrograms = $actor->getPrograms();

        /* dd($actorPrograms); */
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'actorPrograms' => $actorPrograms,
        ]);
    }

}