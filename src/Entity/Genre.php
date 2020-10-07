<?php

namespace App\Entity;

use App\Entity\Traits\SoftDelete;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * deletedAt (datetime)
     */
    use SoftDelete;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Nazwa gatunku nie może być pusta.")
     * @Assert\Length(max=255, maxMessage="Nazwa gatunku nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\-\&]*$/u", message="Nazwa gatunku może zawierać tylko litery, cyfry, białe znaki, myślniki i znak et (&).")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Data rozpoczęcia nie powinna zawierać więcej niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\d\w\s\-\:\.\,]*$/u", message="Data rozpoczęcia zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki, przecinki i dwókropki.")
     */
    private $startedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Data zakończenia nie powinna zawierać więcej niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\d\w\s\-\:\.\,]*$/u", message="Data rozpoczęcia zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki, przecinki i dwókropki.")
     */
    private $endedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="To pole nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\-\&]*$/u", message="To pole może zawierać tylko litery, cyfry, białe znaki, myślniki i znak et (&).")
     */
    private $origin;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/^[\w\d\s\.\^\$\*\,\+\?\(\)\[\{\}\|\^\-\]!@#%&=<>\/\:;\']*$/u", message="Opis posiada niedozwolone znaki.")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, mappedBy="genres")
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity=Book::class, mappedBy="genres")
     */
    private $books;

    /**
     * @ORM\OneToMany(targetEntity=BookGenre::class, mappedBy="genre")
     */
    private $bookGenres;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->books = new ArrayCollection();
        $this->bookGenres = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getBooks(): Collection
    {
        return $this->authors;
    }

    public function addBook(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addGenre($this);
        }

        return $this;
    }

    public function removeBook(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
            $author->removeGenre($this);
        }

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addGenre($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
            $author->removeGenre($this);
        }

        return $this;
    }

    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    public function setStartedAt(?string $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?string
    {
        return $this->endedAt;
    }

    public function setEndedAt(?string $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return Collection|BookGenre[]
     */
    public function getBookGenres(): Collection
    {
        return $this->bookGenres;
    }

    public function addBookGenre(BookGenre $bookGenre): self
    {
        if (!$this->bookGenres->contains($bookGenre)) {
            $this->bookGenres[] = $bookGenre;
            $bookGenre->setGenre($this);
        }

        return $this;
    }

    public function removeBookGenre(BookGenre $bookGenre): self
    {
        if ($this->bookGenres->contains($bookGenre)) {
            $this->bookGenres->removeElement($bookGenre);
            // set the owning side to null (unless already changed)
            if ($bookGenre->getGenre() === $this) {
                $bookGenre->setGenre(null);
            }
        }

        return $this;
    }
}
