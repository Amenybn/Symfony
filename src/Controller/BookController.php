<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\SearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }



    #[Route('/affichBook', name: 'affichBook')]
    public function affichCar(BookRepository $bookRepository, Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
    
        $publishedCount = $bookRepository->sumBooks()['publishedCount'];
        $unpublishedCount = $bookRepository->sumBooks()['unpublishedCount'];
        $scienceFictionCount = $bookRepository->sumScienceFictionBooks();
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2018-12-31');

        $booksBetweenDates = $bookRepository->findBooksBetweenDates($startDate, $endDate); 
        if ($form->isSubmitted() && $form->isValid()) {
            $datainput = $form->get('ref')->getData();
            $book = $bookRepository->searchByRef($datainput);
        } else {
            
           // $book = $bookRepository->listBooksByAuthor();
           // $book = $bookRepository->updateShakespeareBooks();
           
           //$book = $bookRepository->getBooksByDate();
          // $bookRepository->sumScienceFictionBooks();
          $bookRepository->findBooksBetweenDates($startDate, $endDate);
          
           $book = $bookRepository->findByAuthorUsername('William Shakespeare');
           
           
        }
    
        return $this->renderform('book/affichBook.html.twig', [
            'book' => $book,
            'f' => $form,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'scienceFictionCount' => $scienceFictionCount,
            'booksBetweenDates' => $booksBetweenDates,
        ]);
    }
    #[Route('/ajoutBook', name: 'ajoutBook')]
    public function ajoutCar(ManagerRegistry $manag, Request $req): Response
    {   
        $em = $manag->getManager();
        $book = new Book(); 
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) { 
            $em->persist($book); 
            $em->flush();
            return $this->redirect('affichBook'); 
        }
        return $this->renderForm('book/ajoutBook.html.twig', [
            'f' => $form
        ]);
    }
    #[Route('/updateBook/{id}', name: 'updateBook')]
    public function updateCar($id,ManagerRegistry $manag, BookRepository $bookRepository, Request $req): Response
    {
        $em = $manag->getManager();
        $data = $bookRepository->find($id); 
        $form = $this->createForm(BookType::class, $data); 
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($data); 
            $em->flush();
            return $this->redirectToRoute('affichBook'); 
        }
        return $this->renderForm('book/updateBook.html.twig', [
            'f' => $form,
        ]);
    }


    #[Route('/deleteBook/{id}', name: 'deleteBook')]
    public function delete($id, ManagerRegistry $managerRegistry, BookRepository $rep): Response
    {
        $em = $managerRegistry->getManager();
        $id = $rep->find($id);
        $em->remove($id);
        $em->flush();
        return $this->redirectToRoute('affichBook');
    }

}
