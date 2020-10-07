<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rating;
use App\Repository\RatingRepository;
use App\Controller\ErrorController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/oceny", name="rating_")
 */
class RatingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('rating/index.html.twig', [
            'controller_name' => 'RatingController',
        ]);
    }

    /**
     * @Route("/{id}/ocen", name="rate", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function rate($id, Request $request)
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
    		$this->failureAction($request);
    	}

    	$submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('rating_rate'.$book->getId(), $submittedToken)) {
    		$this->failureAction($request);
        }

    	$points = $request->get('points');
    	if (!preg_match('/^\d{1,3}$/', $points)) {
    		$this->failureAction($request);
    	}

        $rating = $this
            ->getDoctrine()
            ->getRepository(Rating::class)
            ->findOneBy([
                'book' => $book,
                'user' => $this->getUser(),
            ])
        ;

        $points = floor($points + 19) / 20;

        if (empty($rating)) {
        	$rating = new Rating();
        	$rating->setPoints($points);
        	$rating->setUser($this->getUser());
        	$rating->setBook($book);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();
        
        } else {
            $rating->setPoints($points);
            $this->getDoctrine()->getManager()->flush();
        }

        $ratings = $this
        	->getDoctrine()
        	->getRepository(Rating::class)
        	->findBy([
        		'book' => $book,
        	])
        ;

        $mean = array_sum(
            array_map(
                fn($r) => $r->getPoints(), 
                $ratings
            )
        ) / count($ratings);

        if ($request->isXmlHttpRequest()) {
        	return new JsonResponse([
                'mean' => $mean,
                'count' => count($ratings),
                'userRating' => $rating->getPoints(),
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/cofnij", name="undo", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function undo($id, Request $request)
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
            $this->failureAction($request);
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('rating_undo'.$book->getId(), $submittedToken)) {
            $this->failureAction($request);
        }

        $rating = $this
            ->getDoctrine()
            ->getRepository(Rating::class)
            ->findOneBy([
                'book' => $book,
                'user' => $this->getUser(),
            ])
        ;

        if (!empty($rating)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rating);
            $entityManager->flush();
        }

        $ratings = $this
            ->getDoctrine()
            ->getRepository(Rating::class)
            ->findBy([
                'book' => $book,
            ])
        ;

        $mean = array_sum(
            array_map(
                fn($r) => $r->getPoints(), 
                $ratings
            )
        ) / ((count($ratings) != 0) ? count($ratings) : 1);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'mean' => $mean,
                'count' => count($ratings),
                'userRating' => 'brak',
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    private function failureAction(Request $request)
    {
    	if ($request->isXmlHttpRequest()) {
    		die;
		}
		$errorController = new ErrorController();
		return $errorController->renderPageNotFound();
    }
}
