<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Genre;
use App\Form\AuthorType;
use App\Controller\ErrorController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/autorzy", name="author_")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
    	$authors = $this
    		->getDoctrine()
    		->getRepository(Author::class)
    		->findBy([
    			'deletedAt' => null,
    		])
    	;

        return $this->render('author/index.html.twig', [
        	'authors' => $authors,
        ]);
    }

    /**
     * @Route("/dodaj", name="create", methods={"GET","POST"})
     */
    public function create(Request $request)
    {
    	$author = new Author();
    	$form = $this->createForm(AuthorType::class, $author);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash('success', 'Nowy autor został pomyślnie dodany.');

            return $this->redirectToRoute('author_show', [
            	'id' => $author->getId(),
            ]);
    	}

    	return $this->render('author/create.html.twig', [
    		'authorForm' => $form->createView(),
    	]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"}, methods="GET")
     */
    public function show($id, Request $request)
    {
    	$author = $this
    		->getDoctrine()
    		->getRepository(Author::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($author)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

    	return $this->render('author/show.html.twig', [
    		'author' => $author,
    	]);
    }

    /**
     * @Route("/{id}/edytuj", name="edit", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function edit($id, Request $request)
    {
    	$author = $this
    		->getDoctrine()
    		->getRepository(Author::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($author)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

    	$form = $this->createForm(AuthorType::class, $author);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Autor został pomyślnie zmodyfikowany.');

            return $this->redirectToRoute('author_show', [
            	'id' => $author->getId(),
            ]);
    	}

    	return $this->render('author/edit.html.twig', [
    		'authorForm' => $form->createView(),
    		'author' => $author,
    	]);
    }

    /**
     * @Route("/{id}/usun", name="delete", requirements={"id"="\d+"}, methods={"GET","DELETE"})
     */
    public function delete($id, Request $request)
    {
    	$author = $this
    		->getDoctrine()
    		->getRepository(Author::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($author)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        if ($this->isCsrfTokenValid('author_delete'.$id, $request->get('_token'))) {
            $author->setDeletedAt();

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Autor został usunięty.');

            return $this->redirectToRoute('author_index');
        }

    	return $this->render('author/delete.html.twig', [
    		'author' => $author,
    	]);
    }
}
