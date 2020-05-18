<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\DeleteType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comment", name="admin_comment_")
 */
class AdminCommentController extends AbstractController
{
    /**
     * @Route("/browse", name="browse")
     */
    public function browse(CommentRepository $commentRepository)
    {
        return $this->render('admin/comment/browse.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function edit(Request $request, Comment $comment)
    {
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirectToRoute('admin_comment_browse');
            }

        // On prépare un formulaire pour supprimer un comment
        $deleteForm = $this->createForm(DeleteType::class, null, [
            'action'=>$this->generateUrl('admin_comment_delete', ['id'=>$comment->getId()])
        ]);

        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'deleteForm'=>$deleteForm->createView(),

        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"POST", "DELETE"})
     */
    public function delete(EntityManagerInterface $em, Comment $comment, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()){
            $em->remove($comment);
            $em->flush();
        }

        // L'objet est ajouté, on redirige vers la liste des commentaires
        return $this->redirectToRoute('admin_comment_browse');
    }
}
