<?php


namespace App\Controller;


use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/edit", name="profile_edit")
     */
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
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