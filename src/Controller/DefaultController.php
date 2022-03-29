<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{_locale}/", name = "app_index", requirements={"_locale": "en|fr"})
     */

    public function index(): Response
    {
        return $this->render('index.html.twig');
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
