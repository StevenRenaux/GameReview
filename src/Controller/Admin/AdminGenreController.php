<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use App\Form\DeleteType;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin/genre", name="admin_")
 */
class AdminGenreController extends AbstractController
{
    /**
     * @Route("/browse", name="genre_browse")
     */
    public function browse(GenreRepository $genreRepository)
    {
        return $this->render('admin/genre/browse.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="genre_edit", requirements={"id": "\d+"})
     */
    public function edit(Request $request, Genre $genre)
    {
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $genre = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirectToRoute('admin_genre_browse');
            }

        // On prépare un formulaire pour supprimer un genre
        $deleteForm = $this->createForm(DeleteType::class, null, [
            'action'=>$this->generateUrl('admin_genre_delete', ['id'=>$genre->getId()])
        ]);

        return $this->render('admin/genre/edit.html.twig', [
            'form' => $form->createView(),
            'deleteForm'=>$deleteForm->createView(),

        ]);
    }

    /**
     * @Route("/add", name="genre_add")
     */
    public function add(Request $request)
    {
        $genre = new Genre;
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $genre = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($genre);
                $em->flush();

                return $this->redirectToRoute('admin_genre_browse');
            }

        return $this->render('admin/genre/add.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/delete/{id}", name="genre_delete", requirements={"id": "\d+"}, methods={"POST", "DELETE"})
     */
    public function delete(EntityManagerInterface $em, Genre $genre, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()){
            $em->remove($genre);
            $em->flush();
        }

        // L'objet est ajouté, on redirige vers la liste des articles
        return $this->redirectToRoute('admin_genre_browse');
    }
}
