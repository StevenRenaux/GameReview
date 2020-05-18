<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;

        $userForm = $this->createForm(CreateAccountType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $userPassword = $userForm->getData()->getPassword();

                $encodedPassword = $passwordEncoder->encodePassword($user, $userPassword);
        
                $user->setPassword($encodedPassword);
                $user->setRoles(['ROLE_USER']);

                // Comme EntityManagerInterface n'est utilisé que dans le if autant le mettre
                // là pour ne pas surcharger le paramconverter et ma méthode en chargement
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre compte à été créé. Vous pouvez dès à présent vous y connecter pour ajouter des commentaires.'
                );

                return $this->redirectToRoute('review_browse');
            }

        return $this->render('user/add.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    /**
     * @Route("/edit/{username}", name="edit")
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('EDIT', $user);

        $userForm = $this->createForm(CreateAccountType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $password = $userForm->get('password')->getData();

            // si le mdp à été changé et est différent de null
            if ($password !== null) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
            }
                $em = $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                    'success',
                    'Votre compte à été modifié.'
                );

                return $this->redirectToRoute('review_browse');
            }

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm->createView(),

        ]);
    }

}
