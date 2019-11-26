<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TestType;

class SimplonController  extends AbstractController
{
    /**
     * @Route("/test", name="simplon_test", methods={"GET","POST"})
     */
    public function test(Request $request)
    {
        $user = new User();
        $form = $this->createForm(TestType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword('password');
            $user->setEnable(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('simplon/test.html.twig', [
            'array' => [
                'a',
                'b'
            ],
            'form' => $form->createView()
        ]);
    }
}