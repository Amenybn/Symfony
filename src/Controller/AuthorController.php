<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinMaxType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/affichAuthor', name: 'affichAuthor')]
    public function affichAuthor(AuthorRepository $authorRepository, Request $req): Response
    {
        $author = $authorRepository->findAll();
        $form = $this->createForm(MinMaxType::class);
        $form->handleRequest($req);
    
        if ($form->isSubmitted()) {
            $min = $form->get('min')->getData();
            $max = $form->get('max')->getData();
            $authors = $authorRepository->searchAuthorByNbBooks($min, $max);
    
            return $this->renderForm('author/affichAuthor.html.twig', [
                'author' => $authors,
                'f' => $form,
            ]);
        }
        
    
        return $this->renderForm('author/affichAuthor.html.twig', [
            'author' => $author,
            'f' => $form,
        ]);
    }





    #[Route('/ajoutAuthor', name: 'ajoutAuthor')]
    public function ajoutAuthor(ManagerRegistry $manag, Request $req): Response
    {   
        $em = $manag->getManager();
        $author = new Author(); 
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) { 
            $em->persist($author); 
            $em->flush();
            return $this->redirect('affichAuthor'); 
        }
        return $this->renderForm('author/ajoutAuthor.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/updateAuthor/{id}', name: 'updateAuthor')]
    public function updateCar($id,ManagerRegistry $manag, AuthorRepository $authorRepository, Request $req): Response
    {
        $em = $manag->getManager();
        $data = $authorRepository->find($id); 
        $form = $this->createForm(AuthorType::class, $data); 
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($data); 
            $em->flush();
            return $this->redirectToRoute('affichAuthor'); 
        }
        return $this->renderForm('author/updateAuthor.html.twig', [
            'f' => $form,
        ]);
    }


    #[Route('/deleteAuthor/{id}', name: 'deleteAuthor')]
    public function delete($id, ManagerRegistry $managerRegistry, AuthorRepository $rep): Response
    {
        $em = $managerRegistry->getManager();
        $id = $rep->find($id);
        $em->remove($id);
        $em->flush();
        return $this->redirectToRoute('affichAuthor');
    }

    #[Route('/showdelete', name: 'showdelete')]
    public function showdelete(AuthorRepository $authorRepository , Request $req): Response
    {    
        $authorRepository->deleteZero();
         $form= $this->createForm(MinMaxType::class);
         $form->handleRequest($req);
         $author = $authorRepository->findAll();
         
         $author = $authorRepository->triaauthor();
         if($form->isSubmitted()){
            $min = $form->get('min')->getData();
            $max = $form->get('max')->getData();
             $authors=$authorRepository->searchNbBook($min , $max);
 
             return $this->renderForm('author/affichAuthor.html.twig', [
                 'author' => $authors,
                 'f'=> $form
             ]);}
        
        return $this->renderForm('author/affichAuthor.html.twig', [
            'author'=> $author,
            'f'=> $form
        ]);
    }
    


}
