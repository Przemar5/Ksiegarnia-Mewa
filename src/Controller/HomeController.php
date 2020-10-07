<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\TagRepository;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Pagination;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index(Request $request, BookRepository $bookRepo, AuthorRepository $authorRepo, GenreRepository $genreRepo, TagRepository $tagRepo)
    {
        $search = [
            'title' => filter_var($request->query->get('tytul'), FILTER_SANITIZE_URL) ?? null,
            'author' => (int) $request->query->get('autor') ?? null,
            'genres' => filter_var($request->query->get('gatunki'), FILTER_SANITIZE_URL) ?? null,
            'tags' => filter_var($request->query->get('tagi'), FILTER_SANITIZE_URL) ?? null,
            'price' => [
                'min' => (int) $request->query->get('cena_min') ?? null,
                'max' => (int) $request->query->get('cena_max') ?? null,
            ],
        ];

        $page = ($request->query->get('strona') > 0)
            ? $request->query->get('strona')
            : 1;
        $booksPerPage = 12;
        
        $data = $bookRepo->search($page, $booksPerPage, $search);
        $books = $data['books'];
        $count = $data['count'];

        $pagesCount = (int) ceil($count / $booksPerPage);
        $pagination = new Pagination();
        $pagination->setPagesCount($pagesCount);
        $pagination->setCurrentPage($page);
        $pagination->setTabsShown(5);

        $authors = $authorRepo->notDeleted();
        $genres = $genreRepo->notDeleted();
        $tags = $tagRepo->notDeleted();

        return $this->render('home/index.html.twig', [
        	'books' => $books,
            'authors' => $authors,
            'genres' => $genres,
            'tags' => $tags,
            'pagination' => $pagination,
        ]);
    }
}
