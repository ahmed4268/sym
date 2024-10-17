<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorControllereController extends AbstractController
{
    #[Route('/authorss', name: 'author_list', methods: ['POST', 'GET'])]
    public function list(Request $request,EntityManagerInterface $entityManager,AuthorRepository $authorRepository): Response
    {

        $minNumber = $request->query->get('minNumber');
        $maxNumber = $request->query->get('maxNumber');
        $authors = null;
        echo $minNumber;
        echo $maxNumber;
        if (!$minNumber && !$maxNumber) {
            $authors = $authorRepository->findAll();
            if (!$authors) {
                return new Response('Aucun auteur trouvé');
            }
            return $this->render('author/list.html.twig', [
                'authors' => $authors,
            ]);
        } else if ($minNumber && !$maxNumber) {
            $query = $entityManager->createQuery(
                'SELECT  b
            FROM App\Entity\Author b
            WHERE b.nbBooks >= :minNumber'
            )->setParameter('minNumber', $minNumber);
            $authors = $query->getResult();
        } else if (!$minNumber && $maxNumber) {
            $query = $entityManager->createQuery(
                'SELECT  b
            FROM App\Entity\Author b
            WHERE b.nbBooks <= :maxNumber'
            )->setParameter('maxNumber', $maxNumber);
            $authors = $query->getResult();
        } else{

            $query = $entityManager->createQuery(
                'SELECT  b
         FROM App\Entity\Author b
         WHERE b.nbBooks BETWEEN :minNumber AND :maxNumber'
            )->setParameter('minNumber', $minNumber)
                ->setParameter('maxNumber', $maxNumber);

        $authors = $query->getResult();
       }

        if (!$authors) {
            return new Response('Aucun auteur trouvé');
        }
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/authorss/add', name: 'author_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
//        $author->setUsername('ali');
//        $author->setEmail('ali@gmaill.com');
//
//
//        $entityManager->persist($author);
//
//        $entityManager->flush();
//
//        return new Response('Auteur ajouté avec succès ');
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/authors/edit/{id}', name: 'author_edit')]
    public function edit(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/authors/delete/{id}', name: 'author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($author);
        $entityManager->flush();


        return $this->redirectToRoute('author_list');
    }

    #[Route('/bookss/{category}', name: 'bookss_count', methods: ['GET'])]
    public function countbycategory(EntityManagerInterface $entityManager, $category): Response
    {
        $query = $entityManager->createQuery(
            'SELECT COUNT(b.id) as c
     FROM App\Entity\Book b
     WHERE b.category = :category'
        )->setParameter('category', $category);

        $result = $query->getSingleScalarResult();
        return new Response('le nombre de livre de la catégorie ' . $category . ' est ' . $result);
    }
    #[Route('/booksss/date', name: 'bookss_date', methods: ['GET'])]
    public function desplaybydate(EntityManagerInterface $entityManager): Response
    {
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2024-10-04');

        $query = $entityManager->createQuery(
            'SELECT b
         FROM App\Entity\Book b
         WHERE b.publicationDate BETWEEN :startDate AND :endDate'
        )->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $books = $query->getResult();

        return $this->render('book/date.html.twig', [
            'books' => $books,
        ]);
}

}
