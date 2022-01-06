<?php
//src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Form\SeasonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *  @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * show all rows from Program's entity
     * 
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * The controller for the program add form
     * Display the form or deal with it
     * 
     * @Route("/new", name="new")
     */

     public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
     {
         $program = new Program();
         // Création du formulaire
         $form = $this->createForm(ProgramType::class, $program);
         //Récupére les données depuis la requéte HTTP
         $form->handleRequest($request);
         // Was the form submitted
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager = $this->getDoctrine()->getManager();
             $slug = $slugify->generate($program->getTitle());
             $program->setSlug($slug);
             //associe l'utilisateur au programe
             $program->setOwner($this->getUser());
             $entityManager->persist($program);
             $entityManager->flush();
             $email = (new Email())
                 ->from($this->getParameter('mailer_from'))
                 ->to('you@example.com')
                 ->subject('new program creation')
                 ->text('A new program has been created')
                 ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
             $mailer->send($email);
             return $this->redirectToRoute('program_index');
         }
         return $this->renderForm('program/new.html.twig', [
             'form' => $form,
         ]);
     }

    /**
     * Getting a program by title
     * 
     * @Route("/show/{slug}", name="show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "title"} }) 
     * @return Response
     */

    public function show(Program $program): Response
    {    

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table'
            );
        }
        $seasons = $this->getDoctrine()->getRepository(Season::class)->findAll();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{slug}/season/{season_slug}", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "title"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_slug": "number"}})
     * @return Response
     */

    public function showSeason(Program $program, Season $season): Response
    {
        /* $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->find($program);

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($season); */

        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/{programId}/season/{seasonId}/episode/{episodeId}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     * @return Response
     */

    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        $program = $this->getDoctrine()->getRepository(Program::class)->find($program);
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($season);
        $episode = $this->getDoctrine()->getRepository(Episode::class)->find($episode);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET", "POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "title"}})
     * @IsGranted ("ROLE_ADMIN")
     */
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }
}
