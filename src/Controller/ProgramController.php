<?php
//src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\User;
use App\Form\SearchProgramFormType;
use App\Form\SeasonType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['Search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * The controller for the program add form
     * Display the form or deal with it
     * @Route("/new", name="new")
     *
     * @IsGranted("ROLE_CONTRIBUTOR")
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
            //Aprés soumission du formulaire, un message flash est envoyé à l'utilisateur
            $this->addFlash('success', 'Une nouvelle série a été rajoutée');
            return $this->redirectToRoute('program_index');
        }
        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
            'program' => $program,
        ]);
    }

    /**
     * Getting a program by title
     *
     * @Route("/show/{slug}", name="show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"} })
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
     * @IsGranted ("ROLE_ADMIN")
     */
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('danger', 'Malheureux, vous venez de supprimer une super série.');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{slug}/season/{season_number}", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_number": "number"}})
     * @return Response
     */

    public function showSeason(Program $program, Season $season): Response
    {

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
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @IsGranted ("ROLE_ADMIN")
     */
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', 'Vous venez d\'éditer une série.');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}/watchlist", name="watchlist", methods={"GET","POST"})
     *
     */
    public function addToWatchList(Program                $program,
                                   EntityManagerInterface $entityManager,
                                   Request                $request): Response
    {
        if ($this->getUser()->isInWatchList($program)) {
            $this->getUser()->removeFromWatchList($program);
        } else {
            $this->getUser()->addToWatchList($program);
        }
        $entityManager->flush();
        return $this->json([
            'isInWatchlist' => $this->getUser()->isInWatchlist($program)
        ]);

    }
}
