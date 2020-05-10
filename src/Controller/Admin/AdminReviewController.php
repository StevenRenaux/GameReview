<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Form\DeleteType;
use App\Form\GameType;
use App\Form\PlatformType;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/review", name="admin_")
 */
class AdminReviewController extends AbstractController
{
    /**
     * @Route("/browse", name="review_browse")
     */
    public function browse(ReviewRepository $reviewRepository)
    {
        return $this->render('admin/review/browse.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="review_edit", requirements={"id": "\d+"})
     */
    public function edit(Request $request, Review $review, Slugger $slugger)
    {
        $reviewForm = $this->createForm(ReviewType::class, $review);

        $reviewForm->handleRequest($request);

            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $review = $reviewForm->getData();

                // On crée le résumé en partant du contenu reçu
                $review->setResume(substr($review->getContent(), 0, 300) . '...');

                // On utilise le service Slugger, il a une méthode toSlug
                // qui calcule le slug de n'importe quelle chaine de caractère
                // On place le résultat de toSlug() dans la propriété $slug de $review
                $review->setSlug($slugger->toSlug($review->getGame()));

                // Comme EntityManagerInterface n'est utilisé que dans le if autant le mettre
                // là pour ne pas surcharger le paramconverter et ma méthode en chargement
                $em = $this->getDoctrine()->getManager();

                // pas besoin de persist car symfo à déjà gardé l'objet Genre grâce au param converter.
                // Si il s'agissait de linstanciation d'un nouvel objet dans la méthode là on aurait dû persist
                //$em->persist($review);
                $em->flush();

                return $this->redirectToRoute('admin_review_browse');
            }

        // On prépare un formulaire pour supprimer une review
        $deleteForm = $this->createForm(DeleteType::class, null, [
            'action'=>$this->generateUrl('admin_review_delete', ['id'=>$review->getId()])
        ]);

        return $this->render('admin/review/edit.html.twig', [
            'reviewForm' => $reviewForm->createView(),
            'deleteForm'=>$deleteForm->createView(),

        ]);
    }

    /**
     * @Route("/add", name="review_add")
     */
    public function add(Request $request, Slugger $slugger)
    {
        $review = new Review;
        $reviewForm = $this->createForm(ReviewType::class, $review);

        $reviewForm->handleRequest($request);

            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $review = $reviewForm->getData();

                // On crée le résumé en partant du contenu reçu
                $review->setResume(substr($review->getContent(), 0, 300) . '...');

                // On utilise le service Slugger, il a une méthode slugify
                // qui calcule le slug de n'importe quelle chaien de caractère
                // On place le résultat de slugify() dans la propriété $slug de $review
                $review->setSlug($slugger->toSlug($review->getGame()));

                // Comme EntityManagerInterface n'est utilisé que dans le if autant le mettre
                // là pour ne pas surcharger le paramconverter et ma méthode en chargement
                $em = $this->getDoctrine()->getManager();

                // pas besoin de persist car symfo à déjà gardé l'objet Genre grâce au param converter.
                // Si il s'agissait de linstanciation d'un nouvel objet dans la méthode là on aurait dû persist
                $em->persist($review);
                $em->flush();

                return $this->redirectToRoute('admin_review_browse');
            }

        return $this->render('admin/review/add.html.twig', [
            'reviewForm' => $reviewForm->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="review_delete", requirements={"id": "\d+"}, methods={"POST", "DELETE"})
     */
    public function delete(EntityManagerInterface $em, Review $review, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()){
            $em->remove($review);
            $em->flush();
        }

        // L'objet est ajouté, on redirige vers la liste des articles
        return $this->redirectToRoute('admin_review_browse');
    }
}
