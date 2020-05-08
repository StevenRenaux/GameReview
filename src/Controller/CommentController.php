<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Review;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment", name="comment_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/add/{slug}", name="add")
     */
    public function add(Request $request, Review $review)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setReview($review);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('review_read', ['slug' => $review->getSlug()]);
        }

        return $this->render('comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
