<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Entity\Traits\SoftDelete;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Podany adres email posiada powiązane z nim konto. Proszę wybrać inny, bądź się zalogować.")
 */
class User implements UserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(max=255, maxMessage="Adres email nie może być dłuższy niż 255 znaków.")
     * @Assert\Email(message="'{{ value }}' nie jest prawidłowym adresem email.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Imię jest wymagane.")
     * @Assert\Length(max=255, maxMessage="Imię nie może być dłuższe niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-]+$/u", message="Imię posiada niedozwolone znaki.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=70)
     * @Assert\NotBlank(message="Nazwisko jest wymagane.")
     * @Assert\Length(max=255, maxMessage="Nazwisko nie może być dłuższe niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-]+$/u", message="Nazwisko posiada niedozwolone znaki.")
     */
    private $surname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Musisz podać swoją płeć.")
     * @Assert\Choice(choices={"male", "female", "other"})
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Musisz podać swoją datę urodzenia.")
     * @Assert\Type("\DateTimeInterface")
     */
    private $birth;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255, maxMessage="Nazwa kraju nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-\s]*$/", message="Nazwa kraju jest nieprawidłowa.")
     */
    private $country;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255, maxMessage="Nazwa miasta nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-\s]*$/u", message="Nazwa miasta jest nieprawidłowa.")
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255, maxMessage="Adres zamieszkania nie może mieć więcej niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\-\.\,\/\s]*$/u", message="Adres posiada niedozwolone znaki.")
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex(pattern="/^\d{2}-\d{3}$/", message="Podany kod pocztowy jest nieprawidłowy.")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Numer kontaktowy jest wymagany.")
     * @Assert\Regex(pattern="/^(?:\+\d{2}\s)?(?:\d{3}-){2}\d{3}$/", message="Podany numer telefonu jest nieprawidłowy.")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex(pattern="/^((?:\+\d{2}\s)?(?:\d{3}-){2}\d{3})?$/", message="Podany numer telefonu jest nieprawidłowy.")
     */
    private $additionalPhone;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Hasło jest wymagane.")
     * @Assert\Length(min=8, minMessage="Hasło powinno mieć co najmniej 8 znaków.")
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/u", 
                        message="Hasło powinno zawierać co najmniej 1 małą literę, 1 dużą literę, 1 cyfrę i 1 znak specjalny.")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="user")
     */
    private $ratings;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdditionalPhone(): ?string
    {
        return $this->additionalPhone;
    }

    public function setAdditionalPhone(?string $additionalPhone): self
    {
        $this->additionalPhone = $additionalPhone;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getBirth(): ?\DateTimeInterface
    {
        return $this->birth;
    }

    public function setBirth(\DateTimeInterface $birth): self
    {
        $this->birth = $birth;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

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
            $rating->setUser($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getUser() === $this) {
                $rating->setUser(null);
            }
        }

        return $this;
    }
}
