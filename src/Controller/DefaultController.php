<?php

namespace App\Controller;

use App\Entity\Program;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name = "app_index")
     */

    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $programs = $entityManager->getRepository(Program::class)->findAll();
        return $this->render('index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @return Response
     * @Route("/thanks", name="app_thanks")
     */
    public function thanks(): Response
    {
        return $this->render('thanks.html.twig');
    }
}
