<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/platform", name="platform_")
 */
class PlatformController extends AbstractController
{
    /**
     * @Route("/read/{name}", name="read")
     */
    public function read(Platform $platform, ReviewRepository $reviewRepository, Request $request, PaginatorInterface $paginator)
    {
        /*
        $reviews = $reviewRepository->findByPlatform($platform->getId());

                return $this->render('platform/read.html.twig', [
            'reviews' => $reviews,
        ]);

*/


        $reviews = $paginator->paginate(
            $reviewRepository->findByPlatform($platform->getId()), // Requête contenant les données à paginer (ici nos reviews par consoles
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );

        // Si le nombre d'objets Review n'est pas à 0 nous affichons le template
        // Controle nécessaire car l'objet Reviews lui n'est jamais vide à cause de $paginator 
        // et on peu alors saisir n'importe quel numéro dans l'url
        if(!empty($reviews->getItems())){
            return $this->render('platform/read.html.twig', [
                'reviews' => $reviews,
            ]);
        }else{
         throw $this->createNotFoundException('pas de reviews');
        }
    }
}
