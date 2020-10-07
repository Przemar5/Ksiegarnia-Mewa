<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestamp
{
	/**
     * @ORM\Column(type="datetime")
     */
	private $createdAt;

	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
	private $updatedAt;

	public function setCreatedAt(): self
	{
		$this->createdAt = new \DateTime();

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setUpdatedAt(): self
	{
		$this->updatedAt = new \DateTime();

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}
}