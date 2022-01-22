<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CommentController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * show all the comments in the episode show
     * @Route ("/comment/index", name="comment_index")
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('episode/show.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * form to add a comment.
     * @Route("/comment/{slug}/new", name="comment_new", methods={"GET", "POST"})
     * @Security ("is_granted('ROLE_ADMIN') or is_granted('ROLE_CONTRIBUTOR')")
     */
    public function new(Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();

        $comment->setEpisode($episode);
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_new', ['slug' => $episode->getSlug()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('episode_show', ['slug' => $episode->getSlug()]);
        }

        return $this->renderForm('comment/_form.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * ajoute une méthode de suppression de commentaire
     * @Route("/comment/{id}", name="comment_delete", methods={"POST"})
     * @ParamConverter("comment", options={"mapping": {"id": "id"}})
     * @IsGranted ("ROLE_ADMIN")
     */
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
    }


}
