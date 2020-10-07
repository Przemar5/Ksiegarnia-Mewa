<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\AuthorRepository;
use App\Repository\GenreRepository;
use App\Repository\TagRepository;
use App\Controller\ErrorController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tagi", name="tag_")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
    	$tags = $this
    		->getDoctrine()
    		->getRepository(Tag::class)
    		->findBy([
    			'deletedAt' => null,
    		], [
                'name' => 'ASC',
            ])
    	;

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

        return $this->render('tag/index.html.twig', [
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
    	$tag = new Tag();
    	$form = $this->createForm(TagType::class, $tag);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            $this->addFlash('success', 'Nowy tag został pomyślnie dodany.');

            return $this->redirectToRoute('tag_show', [
            	'id' => $tag->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('tag/create.html.twig', [
    		'tagForm' => $form->createView(),
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
    	$tag = $this
    		->getDoctrine()
    		->getRepository(Tag::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($tag)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('tag/show.html.twig', [
    		'tag' => $tag,
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
    	$tag = $this
    		->getDoctrine()
    		->getRepository(Tag::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($tag)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

    	$form = $this->createForm(tagType::class, $tag);
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Tag został pomyślnie zmodyfikowany.');

            return $this->redirectToRoute('tag_show', [
            	'id' => $tag->getId(),
            ]);
    	}

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('tag/edit.html.twig', [
    		'tagForm' => $form->createView(),
    		'tag' => $tag,
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
    	$tag = $this
    		->getDoctrine()
    		->getRepository(Tag::class)
    		->findOneBy([
	    		'id' => $id,
	    		'deletedAt' => null,
	    	])
    	;

    	// If resource not found render "page not found"
    	if (empty($tag)) {
    		$errorController = new ErrorController();
    		return $errorController->renderPageNotFound();
    	}

        if ($this->isCsrfTokenValid('tag_delete'.$id, $request->get('_token'))) {
            $tag->setDeletedAt();
            $tag->setName($tag->getName() . '_deleted_' . time());

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tag został usunięty.');

            return $this->redirectToRoute('tag_index');
        }

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

    	return $this->render('tag/delete.html.twig', [
    		'tag' => $tag,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
    	]);
    }
}
