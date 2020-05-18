<?php

namespace App\Controller\Admin;

use App\Entity\Platform;
use App\Form\DeleteType;
use App\Form\PlatformType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/platform", name="admin_platform_")
 */
class AdminPlatformController extends AbstractController
{
    /**
     * @Route("/browse", name="browse")
     */
    public function browse(PlatformRepository $platformRepository)
    {
        return $this->render('admin/platform/browse.html.twig', [
            'platforms' => $platformRepository->findAll(),
        ]);
    }

        /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function edit(Request $request, Platform $platform)
    {
        $form = $this->createForm(PlatformType::class, $platform);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirectToRoute('admin_platform_browse');
            }

        // On prépare un formulaire pour supprimer une console
        $deleteForm = $this->createForm(DeleteType::class, null, [
            'action'=>$this->generateUrl('admin_platform_delete', ['id'=>$platform->getId()])
        ]);

        return $this->render('admin/platform/edit.html.twig', [
            'form' => $form->createView(),
            'deleteForm'=>$deleteForm->createView(),

        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
        $platform = new Platform;
        $form = $this->createForm(PlatformType::class, $platform);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($platform);
                $em->flush();

                return $this->redirectToRoute('admin_platform_browse');
            }

        return $this->render('admin/platform/add.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"POST", "DELETE"})
     */
    public function delete(EntityManagerInterface $em, Platform $platform, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()){
            $em->remove($platform);
            $em->flush();
        }

        // L'objet est ajouté, on redirige vers la liste des consoles
        return $this->redirectToRoute('admin_platform_browse');
    }
}
