<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\DeleteType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/browse", name="browse")
     */
    public function browse(UserRepository $userRepository)
    {
        return $this->render('admin/user/browse.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $password = $userForm->get('password')->getData();

            // si le mdp à été changé et est différent de null
            if ($password !== null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
            }
                $em = $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('admin_user_browse');
            }

        // On prépare un formulaire pour supprimer un utilisateur
        $deleteForm = $this->createForm(DeleteType::class, null, [
            'action'=>$this->generateUrl('admin_user_delete', ['id'=>$user->getId()])
        ]);

        return $this->render('admin/user/edit.html.twig', [
            'userForm' => $userForm->createView(),
            'deleteForm'=>$deleteForm->createView(),

        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {

                $userPassword = $userForm->getData()->getPassword();

                $encodedPassword = $passwordEncoder->encodePassword($user, $userPassword);
    
                $user->setPassword($encodedPassword);

                // Comme EntityManagerInterface n'est utilisé que dans le if autant le mettre
                // là pour ne pas surcharger le paramconverter et ma méthode en chargement
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('admin_user_browse');
            }

        return $this->render('admin/user/add.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"POST", "DELETE"})
     */
    public function delete(EntityManagerInterface $em, User $user, Request $request)
    {
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()){
            $em->remove($user);
            $em->flush();
        }

        // L'objet est ajouté, on redirige vers la liste des articles
        return $this->redirectToRoute('admin_user_browse');
    }
}
