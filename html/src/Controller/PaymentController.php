<?php


namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\ValidateResetPasswordType;
use App\Service\EmailService;
use App\Service\SendgridService;
use App\Util\StripeUtil;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PaymentController extends AbstractController
{

    /**
     * @Route("/payment/{id}", name="app_payment")
     * @IsGranted("ROLE_USER")
     */
    public function payment(Request $request, Product $product): Response
    {
        $user = $this->getUser();

        // Set stripe API key
        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $form = $this->get('form.factory')
            ->createNamedBuilder('payment-form-token')
            ->add('token', HiddenType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            $token = $form->getData()['token'];
            $email = $user->getEmail();

            if (empty($this->getUser()->getStripeCustomerId())) {
                $customer = \Stripe\Customer::create(array(
                    'email' => $email,
                    'source' => $token
                ));
                $stripeCustomerId = $customer->id;

                $user->setStripeCustomerId($stripeCustomerId);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } else {
                $stripeCustomerId = $this->getUser()->getStripeCustomerId();
            }

            try {
                // 4000 0000 0000 3220
                $charge = \Stripe\Charge::create(array(
                    'customer' => $stripeCustomerId,
                    'amount' => $product->getPrice() * 100,
                    'currency' => 'eur'
                ));

            } catch (\Exception $exception) {
                $this->addFlash(
                    'error',
                    'Une erreur est survenu lors du paiement'
                );
                return $this->redirectToRoute('app_payment', array('id' => $product->getId()));
            }


            if (($charge->status == 'succeeded') && ($charge->amount == $product->getPrice() * 100)) {
                return $this->render('payment/thanks.html.twig', [
                ]);
            }

        }

        return $this->render('payment/payment.html.twig', [
            'form' => $form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ]);
    }
}