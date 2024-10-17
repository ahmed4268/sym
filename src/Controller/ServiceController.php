<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return new Response('this is services');

    }

//    #[Route('/services/{name}')]
//  public function services($name):Response
//    {
//        return new Response('hi there '.$name);
//    }
    #[Route('/services/{name}')]
    public function services($name):Response
    {
        return $this->render('service/showService.html.twig', [
            'name' => $name,
        ]);
    }
    #[Route('/redirect')]
    public function  goToIndex():Response
    {
        return $this->redirectToRoute('home_route');

    }

}
