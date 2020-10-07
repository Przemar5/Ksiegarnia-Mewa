<?php

namespace App\Entity;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This class represents order data before confirmation
 */
class SessionOrder
{
	/**
	 * Stores all entity data
	 */
	private $session;

	/**
	 * For selecting ordered books
	 */
	private $bookRepository;

	public function __construct()
	{
		$this->session = new Session();
	}

	public function add(Book $book): void
	{
		$id = $book->getId();

		if ($order = $this->session->get('order')) {
			
			if (isset($order[$id])) {
				$order[$id]++;
			} else {
				$order[$id] = 1;
			}
			$this->session->set('order', $order);
		
		} else {
			$this->session->set('order', [$id => 1]);
		}
	}

	public function remove(Book $book): void
	{
		$id = $book->getId();

		if ($order = $this->session->get('order')) {
			
			if (isset($order[$id])) {
				if ($order[$id] > 1) {
					$order[$id]--;
				
				} else {
					unset($order[$id]);
				}
			}
			$this->session->set('order', $order);
		
		} else {
			$this->session->set('order', [$id => 0]);
		}
	}

	public function getData(): array
	{
		return $this->session->get('order') ?? [];
	}

	public function clear(): void
	{
		$orderData =  $this->session->get('order');

		if (!empty($orderData)) {
			$this->session->set('order', null);
		}
	}

	public function getBooks()
	{
		$data = $this->getData();

		if (!empty($data)) {
			$result = [];
			$books = $this->bookRepository->findForIds(array_keys($data));

			$i = 0;
			foreach ($data as $key => $value) {
				array_filter($books, fn($book) => $book->getId() == $key);

				$result[$key] = [
					'book' => $books[$i++],
					'ammount' => $value,
				];
			}
			return $result;
		}
	}

	public function exists()
	{

	}

	public function fullAmmount(): int
	{
		return array_sum($this->getData()) ?? 0;
	}

	public function setAmmount(Book $book, int $ammount): void
	{
		$id = $book->getId();

		if ($order = $this->session->get('order')) {
			$order[$id] = $ammount;
			$this->session->set('order', $order);
		
		} else {
			$this->session->set('order', [$id => $ammount]);
		}
	}

	public function set()
	{

	}

	public function get(Book $book): int
	{
		return $this->session->get('order')[$book->getId()] ?? 0;
	}

	public function setBookRepository(BookRepository $bookRepository)
	{
		$this->bookRepository = $bookRepository;
	}

	public function getBookRepository(): ?BookRepository
	{
		return $this->bookRepository;
	}
}