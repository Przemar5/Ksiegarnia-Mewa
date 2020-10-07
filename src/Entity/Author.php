<?php

namespace App\Entity;

use App\Entity\Traits\SoftDelete;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Imię może mieć maksymalnie 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\s\d\-\.]*$/u", message="Imię może zawierać tylko litery, cyfry, myślniki i białe znaki.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Nazwisko może mieć maksymalnie 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\s\d\-\.]*$/u", message="Nazwisko może zawierać tylko litery, cyfry, myślniki i białe znaki.")
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Pseudonim może mieć maksymalnie 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\s\d\-\.\,]*$/u", message="Pseudonim może zawierać tylko litery, cyfry, myślniki, kropki, przecinki i białe znaki.")
     */
    private $pseudonym;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Słowna data może mieć maksymalnie 255 znaków.")
     * @Assert\Regex(pattern="/^[\d\w\s\-\:\.\,]*$/u", message="Data urodzenia zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki, przecinki i dwókropki.")
     */
    private $born;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Słowna data może mieć maksymalnie 255 znaków.")
     * @Assert\Regex(pattern="/^[\d\w\s\-\:\.\,]*$/u", message="Data śmierci zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki, przecinki i dwókropki.")
     */
    private $died;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex(pattern="/^[\d\w\s\-\.\,]*$/u", message="Nazwa kraju zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki i przecinki.")
     */
    private $country;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/^[\w\d\s\.\^\$\*\,\+\?\(\)\[\{\}\|\^\-\]!@#%&=<>\/\:;\'\x22]*$/u", message="Opis posiada niedozwolone znaki.")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="authors")
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="author")
     */
    private $books;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->books = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->pseudonym ?? trim($this->name . ' ' . $this->surname);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    public function setPseudonym(?string $pseudonym): self
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }
    
    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function getBorn(): ?string
    {
        return $this->born;
    }

    public function setBorn(?string $born): self
    {
        $this->born = $born;

        return $this;
    }

    public function getDied(): ?string
    {
        return $this->died;
    }

    public function setDied(?string $died): self
    {
        $this->died = $died;

        return $this;
    }
}
