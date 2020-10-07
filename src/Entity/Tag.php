<?php

namespace App\Entity;

use App\Entity\Traits\SoftDelete;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @ORM\Table(name="`tag`")
 * @UniqueEntity(fields={"name"}, message="Podany tag już istnieje. Proszę wybrać inną nazwę.")
 */
class Tag
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
     * @Assert\NotBlank(message="Tag musi posiadać nazwę.");
     * @Assert\Length(max=255, maxMessage="Nazwa tagu nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\s\-\+\.]+$/u", message="Nazwa tagu może zawierać tylko litery, cyfry, białe znaki, myślniki, plusy, znak et (&) i kropki.")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Book::class, mappedBy="tags")
     */
    private $books;

    /**
     * @ORM\OneToMany(targetEntity=BookTag::class, mappedBy="tag")
     */
    private $bookTags;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->bookTags = new ArrayCollection();
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
            $book->addTag($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            $book->removeTag($this);
        }

        return $this;
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
            $bookTag->setTag($this);
        }

        return $this;
    }

    public function removeBookTag(BookTag $bookTag): self
    {
        if ($this->bookTags->contains($bookTag)) {
            $this->bookTags->removeElement($bookTag);
            // set the owning side to null (unless already changed)
            if ($bookTag->getTag() === $this) {
                $bookTag->setTag(null);
            }
        }

        return $this;
    }
}
