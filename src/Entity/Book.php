<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Entity\Traits\SoftDelete;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * createdAt (datetime) and updatedAt (datetime)
     */
    use Timestamp;
    
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
     * @Assert\NotBlank(message="Książka musi posiadać tytuł.");
     * @Assert\Length(max=255, maxMessage="Tytuł nie może być dłuższy niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\.\^\$\*\,\+\?\(\)\[\{\|\^\-\]]*$/u", message="Tytuł posiada niedozwolone znaki.")
     */
    private $title;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="ISBN nie może być dłuższy niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\.\^\$\*\,\+\?\(\)\[\{\|\^\-\]]*$/u", message="ISBN posiada niedozwolone znaki.")
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Data wydania książki nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\d\w\s\-\:\.\,]*$/u", message="Data wydania zawiera posiada niedozwolone znaki. Dostępne znaki to litery, cyfry, białe znaki, myślniki, kropki, przecinki i dwókropki.")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $available;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $reserved;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="books")
     */
    private $genres;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/^[\w\d\s\.\^\$\*\,\+\?\(\)\[\{\}\|\^\-\]!@#%&=<>\/\:;\'\x22]*$/u", message="Opis posiada niedozwolone znaki.")
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?$/")
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="books")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity=Language::class, mappedBy="books")
     */
    private $languages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="Ścieżka do zdjęcia nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\.\/]*$/u", message="Ścieżka do zdjęcia posiada niedozwolone znaki.")
     */
    private $cover;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Choice(choices={"soft", "hard"})
     */
    private $coverType;

    private $defaultCover = 'noimage.png';

    /**
     * Directory for cover images
     */
    private $coverDir = '/public/images/app/book_covers/';

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="book")
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity=BookGenre::class, mappedBy="book")
     */
    private $bookGenres;

    /**
     * @ORM\OneToMany(targetEntity=BookTag::class, mappedBy="book")
     */
    private $bookTags;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->bookGenres = new ArrayCollection();
        $this->bookTags = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(?bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
            $language->addBook($this);
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
            $language->removeBook($this);
        }

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getReleasedAt(): string
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(string $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addBook($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeBook($this);
        }

        return $this;
    }

    public function getReserved()
    {
        return $this->reserved;
    }

    public function setReserved($reserved): self
    {
        $this->reserved = $reserved;

        return $this;
    }

    public function getCoverType(): ?string
    {
        return $this->coverType;
    }

    public function setCoverType(?string $coverType): self
    {
        $this->coverType = $coverType;

        return $this;
    }

    public function getDefaultCover(): ?string
    {
        return $this->defaultCover;
    }

    public function setDefaultCover(string $defaultCover): self
    {
        $this->defaultCover = $defaultCover;

        return $this;
    }

    public function getCoverDir(): ?string
    {
        return $this->coverDir;
    }

    public function setCoverDir(string $coverDir): self
    {
        $this->coverDir = $coverDir;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setBook($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getBook() === $this) {
                $rating->setBook(null);
            }
        }

        return $this;
    }

    public function getUserRating(?User $user): ?Rating
    {
        if (empty($user)) return null;
        return $this->ratings->filter(function ($r) use ($user) {
            return $r->getUser() === $user;
        })[0];
    }

    public function getRatingMean(): int
    {
        $count = ($this->ratings->count() !== 0) ? $this->ratings->count() : 1;
        return array_sum($this->ratings->toArray()) / $count;
    }

    /**
     * @return Collection|BookGenre[]
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    /**
     * @return Collection|BookTag[]
     */
    public function getBookTags(): Collection
    {
        return $this->bookTags;
    }

    public function addBookTag(BookTag $bookTag): self
    {
        if (!$this->bookTags->contains($bookTag)) {
            $this->bookTags[] = $bookTag;
            $bookTag->setBook($this);
        }

        return $this;
    }

    public function removeBookTag(BookTag $bookTag): self
    {
        if ($this->bookTags->contains($bookTag)) {
            $this->bookTags->removeElement($bookTag);
            // set the owning side to null (unless already changed)
            if ($bookTag->getBook() === $this) {
                $bookTag->setBook(null);
            }
        }

        return $this;
    }
}
