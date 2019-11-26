<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\ValidateResetPasswordType;
use App\Service\EmailService;
use App\Service\SendgridService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{

    /**
     * @Route("/reset", name="app_reset")
     */
    public function reset(Request $request, EmailService $emailService): Response
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($form->getData()['email']);

            if (!$user) {
                $this->addFlash('danger', 'Votre email est inconnu.');

                return $this->redirectToRoute('app_reset');
            }

            $token = md5(uniqid());
            $user->setToken($token);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // send an email
            $url =  $this->generateUrl('app_reset_validation', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $emailService->sendResetPwd('Mise à jour de votre mot de passe', 'noreply@localhost', $user->getEmail(), ['user' => $user, 'url' => $url]);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/validation", name="app_reset_validation")
     */
    public function resetValidation(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ValidateResetPasswordType::class);
        $form->handleRequest($request);

        if ($request->get('token')) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByToken($request->get('token'));
            if (!$user) {
                $this->addFlash('danger', 'Votre lien est invalide');

                return $this->redirectToRoute('app_home');
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByToken($request->get('token'));

            if ($user) {
                // Reset Token dans set DateTime validation
                $user->setToken(null);
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Merci de vous connecter');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('front/password/reset_validation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}