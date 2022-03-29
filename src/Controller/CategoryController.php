<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("{_locale}/category", name="category_", requirements={"_locale": "en|fr"})
 */
class CategoryController extends AbstractController
{

    /**
     * show all rows from Category's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */

    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render(
            'category/index.html.twig',
            ['categories' => $categories]
        );
    }

    /**
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     * @IsGranted("ROLE_ADMIN")
     */

    public function new(Request $request): Response
    {
        //instanciation d'un nouvel objet de la classe Category
        $category = new Category();
        //création du formulaire associé
        $form = $this->createForm(CategoryType::class, $category);
        //Get data from HTTP request
        $form->handleRequest($request);
        // was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category object
            $entityManager->persist($category);
            // flush the entity
            $entityManager->flush();
            $this->addFlash('success', 'Bravo, tu as rajouté une nouvelle catégorie.');
            // And redirect to a route that display the result
            return $this->redirectToRoute('category_index');
        }

        //envoi du formulaire sur la vue twig
        return $this->renderForm('category/new.html.twig', [
            "form" => $form,
        ]);
    }




    /**
     * @Route("/{categoryName}", name="show")
     *@return Response A response instance
     */

    public function show(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for category ' . $categoryName
            );
        } else {
            $programs = $this->getDoctrine()->getRepository(Program::class)->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );
        }
        return $this->render('category/show.html.twig', [
            'programs' => $programs,
        ]);
    }
}
