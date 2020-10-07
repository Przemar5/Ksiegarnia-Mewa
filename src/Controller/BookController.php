<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\GenreRepository;
use App\Repository\TagRepository;
use App\Form\BookType;
use App\Controller\ErrorController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileUploader;

/**
 * @Route("/ksiazki", name="book_")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$books = $this
    		->getDoctrine()
    		->getRepository(Book::class)
    		->findBy([
    			'deletedAt' => null,
    		])
    	;

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

        return $this->render('book/index.html.twig', [
        	'books' => $books,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/dodaj", name="create", methods={"GET","POST"})
     */
    public function create(Request $request, FileUploader $uploader, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$book = new Book();
    	$form = $this->createForm(BookType::class, $book);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['coverImage']->getData();
            // Get "delete file" checkbox value
            $currentCover = $book->getCover();

            $uploader->setBaseDir($this->getParameter('kernel.project_dir'));

            if (!empty($file)) {
                $filename = $uploader->upload($file, $book->getCoverDir());
                $book->setCover($filename);

            } else {
                $book->setCover($book->getDefaultCover());
            }

            $book->setReserved(0);
            $book->setCreatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Książka została pomyślnie dodana.');

            return $this->redirectToRoute('book_show', [
            	'id' => $book->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('book/create.html.twig', [
    		'bookForm' => $form->createView(),
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
    	$book = $this
    		->getDoctrine()
    		->getRepository(Book::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($book)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('book/show.html.twig', [
            'book' => $book,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/{id}/edytuj", name="edit", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function edit($id, Request $request, FileUploader $uploader, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$book = $this
    		->getDoctrine()
    		->getRepository(Book::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($book)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

    	$form = $this->createForm(BookType::class, $book);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['coverImage']->getData();
            // Get "delete file" checkbox value
            $deleteFile = $form['deleteCover']->getData();
            $uploader->setBaseDir($this->getParameter('kernel.project_dir'));

            if ($book->getCover() != $book->getDefaultCover() && ($deleteFile || !empty($file))) {
                $uploader->delete($book->getCoverDir().$book->getCover());
                $book->setCover($book->getDefaultCover());
            }

            if (!empty($file)) {
                $filename = $uploader->upload($file, $book->getCoverDir());
                $book->setCover($filename);
            }

            $book->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Książka została pomyślnie zmodyfikowana.');

            return $this->redirectToRoute('book_show', [
            	'id' => $book->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('book/edit.html.twig', [
    		'bookForm' => $form->createView(),
    		'book' => $book,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/{id}/usun", name="delete", requirements={"id"="\d+"}, methods={"GET","DELETE"})
     */
    public function delete($id, Request $request, FileUploader $uploader, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$book = $this
    		->getDoctrine()
    		->getRepository(Book::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($book)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        if ($this->isCsrfTokenValid('book_delete'.$id, $request->get('_token'))) {
            $uploader->setBaseDir($this->getParameter('kernel.project_dir'));

            if ($book->getCover() != $book->getDefaultCover()) {
                $uploader->delete($book->getCoverDir().$book->getCover());
            }

            $book->setDeletedAt();

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Książka została usunięta.');

            return $this->redirectToRoute('book_index');
        }

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('book/delete.html.twig', [
    		'book' => $book,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }
}
