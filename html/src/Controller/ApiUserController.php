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
     * @param Request $request
     * @return View
     */
    public function getUser()
    {
        return View::create(['test'], Response::HTTP_FOUND);
    }
}