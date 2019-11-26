<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'products' => $productRepository->findByState(0),
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('default/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/products", name="product_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function products(ProductRepository $productRepository): Response
    {
        return $this->render('default/products.html.twig', [
            'products' => $productRepository->findByCreatedBy($this->getUser()),
        ]);
    }

    /**
     * @Route("/products/new", name="product_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, FileUploaderService $fileUploader): Response
    {
        $fileUploader->setTargetDirectory($this->getParameter('product_directory'));
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setCreatedAt(new \DateTime());
            $product->setCreatedBy($this->getUser());
            $product->setState(0);

            foreach ($product->getPictures() as $item) {

                if ($item->getPath() instanceof UploadedFile) {
                    $fileName = $fileUploader->upload($item->getPath());
                    $item->setPath($fileName);
                }

                $item->setProduct($product);
                $entityManager->persist($item);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('default/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
