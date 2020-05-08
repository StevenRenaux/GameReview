<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="review_")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="browse")
     */
    public function browse(ReviewRepository $reviewRepository)
    {
        return $this->render('review/browse.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    /**
     * @Route("review/{slug}", name="read")
     */
    public function read(Review $review)
    {
        return $this->render('review/read.html.twig', [
            'review' => $review,
        ]);
    }
}
