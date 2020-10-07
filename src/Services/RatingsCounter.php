<?php

namespace App\Services;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\RatingRepository;

class RatingsCounter
{
	public function __construct(Book $book, User $user)
	{
		$this->book = $book;
		$this->user = $user;
	}

	public function load()
	{
		
	}

	public function get(int $score)
	{
		
	}
}