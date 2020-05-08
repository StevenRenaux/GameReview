<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="review_")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="browse",)
     */
    public function browse(ReviewRepository $reviewRepository,Request $request,  PaginatorInterface $paginator)
    {
        $reviews = $paginator->paginate(
            $reviewRepository->findAll(), // Requête contenant les données à paginer (ici nos reviews)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );

        // Si le nombre d'objets Review n'est pas à 0 nous affichons le template
        // Controle nécessaire car l'objet Reviews lui n'est jamais vide à cause de $paginator 
        // et on peu alors saisir n'importe quel numéro dans l'url
        if(!empty($reviews->getItems())){
            return $this->render('review/browse.html.twig', [
                'reviews' => $reviews,
            ]);
        }else{
         throw $this->createNotFoundException('pas de reviews');
        }
    }

    /**
     * @Route("/review/{slug}", name="read")
     */
    public function read(Review $review)
    {
        return $this->render('review/read.html.twig', [
            'review' => $review,
        ]);
    }
}
