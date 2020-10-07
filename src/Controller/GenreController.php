<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\AuthorRepository;
use App\Repository\GenreRepository;
use App\Repository\TagRepository;
use App\Form\GenreType;
use App\Controller\ErrorController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gatunki", name="genre_")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$genres = $this
    		->getDoctrine()
    		->getRepository(Genre::class)
    		->findBy([
    			'deletedAt' => null,
    		])
    	;

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

        return $this->render('genre/index.html.twig', [
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/dodaj", name="create", methods={"GET","POST"})
     */
    public function create(Request $request, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$genre = new Genre();
    	$form = $this->createForm(GenreType::class, $genre);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            $this->addFlash('success', 'Nowy gatunek został pomyślnie dodany.');

            return $this->redirectToRoute('genre_show', [
            	'id' => $genre->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('genre/create.html.twig', [
    		'genreForm' => $form->createView(),
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"}, methods="GET")
     */
    public function show($id, Request $request, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$genre = $this
    		->getDoctrine()
    		->getRepository(Genre::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($genre)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('genre/show.html.twig', [
    		'genre' => $genre,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }

    /**
     * @Route("/{id}/edytuj", name="edit", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function edit($id, Request $request, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$genre = $this
    		->getDoctrine()
    		->getRepository(Genre::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($genre)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

    	$form = $this->createForm(GenreType::class, $genre);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Gatunek został pomyślnie zmodyfikowany.');

            return $this->redirectToRoute('genre_show', [
            	'id' => $genre->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('genre/edit.html.twig', [
    		'genreForm' => $form->createView(),
    		'genre' => $genre,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }

    /**
     * @Route("/{id}/usun", name="delete", requirements={"id"="\d+"}, methods={"GET","DELETE"})
     */
    public function delete($id, Request $request, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$genre = $this
    		->getDoctrine()
    		->getRepository(Genre::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($genre)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        if ($this->isCsrfTokenValid('genre_delete'.$id, $request->get('_token'))) {
            $genre->setDeletedAt();

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Gatunek został usunięty.');

            return $this->redirectToRoute('genre_index');
        }

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('genre/delete.html.twig', [
    		'genre' => $genre,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }
}
