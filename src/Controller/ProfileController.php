<?php


namespace App\Controller;


use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/edit", name="profile_edit")
     */
    public function edit(Request $request, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailConstraint = new Assert\Email();
            // all constraint "options" can be set this way
            $emailConstraint->message = 'Invalid email address';

            // use the validator to validate the value
            $errors = $validator->validate(
                $this->getUser()->getUsername(),
                $emailConstraint
            );

            if (count($errors) > 0) {
                $errorMessage = $errors[0]->getMessage();

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'emailerror' => $errorMessage
                ]);

            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/", name="profile_index")
     */
    public function index(Request $request): Response
    {   /*Als niet ingelogt, mag niet komen*/
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /*Als de role admin ga naar user index*/
        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            return $this->redirectToRoute('user_index');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}