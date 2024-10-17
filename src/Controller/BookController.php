<?php

declare(strict_types=1);

namespace App\Controller;

// src/Controller/BookController.php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function edit(int $id, Request $request, BookRepository $bookRepository, EntityManagerInterface $entityManager,AuthorRepository $authorRepository): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }
        $authorId = $book->getAuthor()->getId();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            if($authorId != $request->request->get('author')) {
                $author = $authorRepository->find($authorId);
                $author->setNbBooks($author->getNbBooks() - 1);
                $author = $authorRepository->find($request->request->get('author'));
                $author->setNbBooks($author->getNbBooks() + 1);
                $book->setAuthor($author);
            }


            $book->setEnabled(true);
            $book->setCategory($request->request->get('category'));
            $entityManager->persist($book);

            $entityManager->flush();

            return $this->redirectToRoute('book_list');
        }
        $authors = $authorRepository->findAll();

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
          'authors' => $authors,
        ]);
    }
    // src/Controller/BookController.php

    #[Route('/book/{id}', name: 'book_show')]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
    #[Route('/books/new', name: 'book_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
//        echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);


        $form->handleRequest($request);


        if ($form->isSubmitted() ) {
            $author = $authorRepository->find($request->request->get('author'));
//            echo "Author ID: " . $author->getId() . "\n";
//            echo "Author Name: " . $author->getUsername() . "\n";
//            echo "Number of Books: " . $author->getNbBooks() . "\n";
            $author->setNbBooks($author->getNbBooks() + 1);
            $book->setAuthor($author);
            $book->setEnabled(true);
            $book->setCategory($request->request->get('category'));
            $entityManager->persist($book);
//            $entityManager->persist($author);

            $entityManager->flush();

            return $this->redirectToRoute('book_list');
        }

        $authors = $authorRepository->findAll();

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
            'authors' => $authors,
        ]);
    }
    #[Route('/book/delete/{id}', name: 'book_delete', methods: ['POST'])]
    public function delete(int $id, BookRepository $bookRepository, EntityManagerInterface $entityManager,AuthorRepository $authorRepository): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }
        $author = $authorRepository->find($book->getAuthor()->getId());
        $author->setNbBooks($author->getNbBooks() - 1);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_list');
    }

    #[Route('/books', name: 'book_list')]
    public function list(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        $enabledBooks = array_filter($books, function($book) {
            return $book->isEnabled();
        });
        $notEnabledBooks = array_filter($books, function($book) {
            return !$book->isEnabled();
        });
        if(!$enabledBooks) {
            return new Response('Aucun livre trouvé');
        }
        return $this->render('book/list.html.twig', [
            'books' => $enabledBooks,
            'enabledCount' => count($enabledBooks),
            'notEnabledCount' => count($notEnabledBooks),
        ]);
    }

}
