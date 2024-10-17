<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/authorr/{name}')]
    public function showAuthor($name):Response
    {
        return new Response('hi there '.$name);
    }
    #[Route( '/authordetail/:author',name:'showAuthor')]
    public function authorDetails($author):Response
    {
        echo $author;


        return $this->render('service\showAuthor.html.twig',['author' => $author]);
    }
    #[Route('/authorlist')]
    public function listAuthors():Response
    {
//        $authors = null ;
        $authors = array(
            array('id' => 1, 'picture' => '/images/victor.webp','username' => 'Victor Hugo', 'email' =>
                'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/willan.webp','username' => ' William Shakespeare', 'email' =>
                ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/taha.webp','username' => 'Taha Hussein', 'email' =>
                'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        return $this->render('service/list.html.twig', [
            'authors' => $authors,
        ]);
    }
}
