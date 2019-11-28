<?php
namespace App\Controller;

use App\Form\UserApiType;
use App\Repository\UserRepository;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;

/**
 * User apIcontroller.
 * @Route("/api/v1", name="api_")
 */
class ApiUserController extends FOSRestController
{
    /**
     * Test
     * @Rest\Get("/test")
     * @return View
     */
    public function getUser()
    {
        return View::create(['test'], Response::HTTP_I_AM_A_TEAPOT);
    }

    /**
     * Creates a User resource
     * @Rest\Post("/user")
     * @param Request $request
     * @return View
     */
    public function postUser(Request $request): View
    {
        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return View::create($user, Response::HTTP_CREATED);
    }

    /**
     * Test
     * @Rest\Get("/users")
     * @return View
     */
    public function getUsers(UserRepository $userRepository)
    {
        $emails = [];
        $users = $userRepository->findAll();
        foreach ($users as  $u) {
            $emails[] = $u->getEmail();
        }

        return View::create($emails, Response::HTTP_FOUND);
    }

     /**
     * Create User.
     * @Rest\Post("/user-type")
     *
     * @return Response
     */
    public function postUserType(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserApiType::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}