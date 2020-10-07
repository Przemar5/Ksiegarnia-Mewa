<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
	private $deletedAt;

	public function setDeletedAt(): self
	{
		$this->deletedAt = new \DateTime();

		return $this;
	}

	public function getDeletedAt(): ?\DateTimeInterface
	{
		return $this->deletedAt;
	}
}