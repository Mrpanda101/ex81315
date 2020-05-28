<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN",statusCode=404, message="No access for User! You should't be here! >:(-I-<")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {


        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailConstraint = new Assert\Email();
            // all constraint "options" can be set this way
            $emailConstraint->message = 'Invalid email address';

            // use the validator to validate the value
            $errors = $validator->validate(
                $user->getUsername(),
                $emailConstraint
            );

            if (count($errors) > 0) {
                $errorMessage = $errors[0]->getMessage();

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'emailerror' => $errorMessage
                ]);

            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit_admin(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailConstraint = new Assert\Email();
            // all constraint "options" can be set this way
            $emailConstraint->message = 'Invalid email address';

            // use the validator to validate the value
            $errors = $validator->validate(
                $user->getUsername(),
                $emailConstraint
            );

            if (count($errors) > 0) {
                $errorMessage = $errors[0]->getMessage();

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'emailerror' => $errorMessage
                ]);

            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');

        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user ,TokenStorageInterface $tokenStorage): Response
    {
        $currentUserid = $this->getUser()->getId();
        $userid= $user->getId();
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        if ($currentUserid === $userid) {
            $tokenStorage->setToken(null);
            return $this->redirectToRoute('homepage');
        }

        return $this->redirectToRoute('user_index');
    }
}
