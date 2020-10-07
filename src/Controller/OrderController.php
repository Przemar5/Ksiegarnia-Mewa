<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Order;
use App\Entity\SessionOrder;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;
use App\Form\OrderType;
use App\Controller\ErrorController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/zamowienie", name="order_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/indeks", name="index")
     */
    public function index()
    {
        $orders = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findByIn('status', ['confirmed', 'send'])
        ;

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/", name="show", methods="GET")
     */
    public function show(Request $request)
    {
        $user = $this->getUser();
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'user' => $user,
                'status' => 'confirmed',
            ])
        ;

        if (!empty($order)) {
            return $this->redirectToRoute('order_summary', [
                'id' => $order->getId(),
            ]);
        }

        $order = new SessionOrder();
        $order->setBookRepository($this->getDoctrine()->getRepository(Book::class));

    	return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/uzupelnij-dane", name="complete", methods={"GET","POST"})
     */
    public function complete(Request $request)
    {
        $user = $this->getUser();
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'user' => $user,
                'status' => 'confirmed',
            ])
        ;

        if (!empty($order)) {
            return $this->redirectToRoute('order_summary', [
                'id' => $order->getId(),
            ]);
        
        } else {
            $order = $this
                ->getDoctrine()
                ->getRepository(Order::class)
                ->findOneBy([
                    'user' => $user,
                    'status' => 'before confirmation',
                ])
            ;

            if (empty($order)) {
                $order = new Order();
            }
        }

        if (empty($order->getName()))               $order->setName($user->getName());
        if (empty($order->getSurname()))            $order->setSurname($user->getSurname());
        if (empty($order->getCountry()))            $order->setCountry($user->getCountry());
        if (empty($order->getCity()))               $order->setCity($user->getCity());
        if (empty($order->getAddress()))            $order->setAddress($user->getAddress());
        if (empty($order->getPostalCode()))         $order->setPostalCode($user->getPostalCode());
        if (empty($order->getPhone()))              $order->setPhone($user->getPhone());
        if (empty($order->getAdditionalPhone()))    $order->setAdditionalPhone($user->getAdditionalPhone());

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sessionOrder = new SessionOrder();
            $order->setProducts($sessionOrder->getData());
            $order->setStatus('before confirmation');
            $order->setUser($this->getUser());
            $ids = array_keys($order->getProducts());

            $books = $this
                ->getDoctrine()
                ->getRepository(Book::class)
                ->findForIds($ids)
            ;
            $price = 0;
            foreach ($books as $book) {
                $price += $book->getPrice() * $order->getProducts()[$book->getId()];
            }
            $order->setPrice($price);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('order_confirm', [
                'id' => $order->getId(),
            ]);
        }

        return $this->render('order/complete.html.twig', [
            'orderPlaceForm' => $form->createView(),
        ]);
    }

    /** 
     * @Route("/{id}/potwierdz", name="confirm", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function confirm($id, Request $request)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'user' => $this->getUser(),
                'status' => 'before confirmation',
            ])
        ;

        if (empty($order)) {
            return $this->redirectToRoute('order_show');
        }

        $ids = array_keys($order->getProducts());

        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findForIds($ids)
        ;

        $submittedToken = $request->request->get('_token') ?? null;

        if (!empty($submittedToken) && $this->isCsrfTokenValid('order_confirm'.$order->getId(), $submittedToken)) {
            $order->setStatus('confirmed');

            $sessionOrder = new SessionOrder();

            foreach ($sessionOrder->getData() as $id => $ammount) {
                foreach ($books as $book) {
                    if ($book->getId() == $id) {
                        $book->setReserved($book->getReserved() + $ammount);
                        break;
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_summary', [
                'id' => $order->getId(),
            ]);
        }

        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
            'books' => $books,
        ]);
    }

    /**
     * @Route("/{id}/podsumowanie", name="summary", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function summary($id, Request $request)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'id' => $id,
                'status' => 'confirmed',
            ])
        ;

        if (empty($order) || ($order->getUser() !== $this->getUser() && !$user->hasRole('ROLE_ADMIN'))) {
            return $this->redirectToRoute('order_show');
        }

        $ids = array_keys($order->getProducts());

        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findForIds($ids)
        ;

        return $this->render('order/summary.html.twig', [
            'order' => $order,
            'books' => $books,
        ]);
    }

    /**
     * @Route("/{id}/dodaj", name="add_product", requirements={"id"="\d+"}, methods="POST")
     */
    public function addOne($id, Request $request)
    {
    	$book = $this
    		->getDoctrine()
    		->getRepository(Book::class)
    		->findOneBy([
    			'id' => $id,
    		])
    	;

        if (empty($book)) {
            die;
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('add_product'.$book->getId(), $submittedToken)) {
            die;
        }

        $order = new SessionOrder();

        if ($book->getQuantity() - $book->getReserved() > $order->get($book)) {
        	$order->add($book);
        }

    	if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'ammount' => $order->get($book),
                'fullAmmount' => $order->fullAmmount(),
            ]);
    	}
        
   		return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/odejmij", name="remove_product", requirements={"id"="\d+"}, methods="POST")
     */
    public function removeOne($id, Request $request)
    {
        $book = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findOneBy([
                'id' => $id,
            ])
        ;

        if (empty($book)) {
            die;
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('remove_product'.$book->getId(), $submittedToken)) {
            die;
        }

        $order = new SessionOrder();

        if (0 < $order->get($book)) {
            $order->remove($book);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'ammount' => $order->get($book),
                'fullAmmount' => $order->fullAmmount(),
            ]);
        }

    	return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/zmien-ilosc", name="change_product_ammount", requirements={"id"="\d+"}, methods="POST")
     */
    public function changeAmmount($id, Request $request)
    {
        $book = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findOneBy([
                'id' => $id,
            ])
        ;

        if (empty($book)) {
            die;
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('change_product_ammount'.$book->getId(), $submittedToken)) {
            die;
        }

        $ammount = $request->get('ammount');

        if (!preg_match('/\d+/', $ammount)) {
            die;
        }

        $order = new SessionOrder();

        if ($book->getQuantity() - $book->getReserved() < $ammount) {
            $ammount = $book->getQuantity() - $book->getReserved();

        } else if ($ammount < 0) {
            $ammount = 0;
        }

        $order->setAmmount($book, $ammount);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'ammount' => $order->get($book),
                'fullAmmount' => $order->fullAmmount(),
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{id}/zmien-status", name="change_status", methods="POST", requirements={"id"="\d+"})
     */
    public function changeStatus($id, Request $request, MailerInterface $mailer, BookRepository $bookRepo)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'id' => $id,
            ])
        ;
        $status = $request->get('order_status');

        if (empty($order)) {
            if ($request->isXmlHttpRequest()) {
                die;
            }

            $errorController = new ErrorController();
            return $errorController->renderPageNotFound();
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('order_change_status'.$order->getId(), $submittedToken)) {
            if ($request->isXmlHttpRequest()) {
                die;
            }
            
            $errorController = new ErrorController();
            return $errorController->renderPageNotFound();
        }

        if ($status == 'send') {
            $order->setStatus($status);
            $this->getDoctrine()->getManager()->flush();

            if ($order->getStatus() == 'send') {
                $email = (new Email())
                    ->from(new Address('ksiegarniamewa@gmail.com', 'Księgarnia Mewa'))
                    ->to($order->getUser()->getEmail())
                    ->subject('Zamówienie')
                    ->html('<p>Książki, które zamówiłeś zostały wysłane. Spodziewaj się przesyłki w ciągu najbliższych kilku dni.</p><p>Życzymy miłego dnia!</p><p>Pracownicy Księgarni Mewa</p>');

                $mailer->send($email);
            }

        } elseif ($status == 'completed') {
            $order->setStatus($status);
            $books = $bookRepo->findForIds($order->getBookIds());

            foreach ($books as $book) {
                $ammount = $order->getBookAmmount($book->getId());
                $book->setReserved($book->getReserved() - $ammount);
                $book->setQuantity($book->getQuantity() - $ammount);
            }
            
            $this->getDoctrine()->getManager()->flush();
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => $order->getStatus(),
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/usun", name="delete", methods={"GET","DELETE"})
     */
    public function delete(Request $request)
    {
        $order = $this
            ->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy([
                'user' => $this->getUser(),
                'status' => 'before confirmation',
            ])
        ;

        if (empty($order)) {
            return $this->redirectToRoute('home');
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('order_delete'.$order->getId(), $submittedToken)) {

            $sessionOrder = new SessionOrder();
            $sessionOrder->clear();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($order);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('order/delete.html.twig', [
            'order' => $order,
        ]);
    }
}
