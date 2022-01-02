<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Program;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/comment/new", name="comment_new", methods={"GET", "POST"})
     * @IsGranted ("ROLE_ADMIN")
     * @IsGranted ("ROLE_CONTRIBUTOR")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->renderForm('comment/_form.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * ajoute une méthode de suppression de commentaire
     * @Route("/comment/{id}", name="delete", methods={"POST"})
     * @IsGranted ("ROLE_ADMIN")
     */
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!($this->getUser() == $comment->getAuthor())) {
            throw new AccessDeniedException("only the author can delete his comment");
        }
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }


}