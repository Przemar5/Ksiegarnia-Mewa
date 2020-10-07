<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * 
     * @Assert\Length(max=255, maxMessage="Nazwa kraju nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-\s]*$/", message="Nazwa kraju jest nieprawidłowa.")
     */
    private $country;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=255, maxMessage="Nazwa miasta nie może być dłuższa niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\-\s]*$/u", message="Nazwa miasta jest nieprawidłowa.")
     */
    private $city;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max=255, maxMessage="Adres zamieszkania nie może mieć więcej niż 255 znaków.")
     * @Assert\Regex(pattern="/^[\w\d\-\.\,\/\s]*$/u", message="Adres posiada niedozwolone znaki.")
     */
    private $address;

    /**
     * @ORM\Column(type="string")
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
     * @Assert\Regex(pattern="/^(?:\+\d{2}\s)?(?:\d{3}-){2}\d{3}$/", message="Podany numer telefonu jest nieprawidłowy.")
     */
    private $additionalPhone;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\Column(type="json")
     */
    private $products;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Musisz wybrać metodę płatności.")
     * @Assert\Choice(choices={"Płatność przy odbiorze"})
     */
    private $paymentForm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function __construct()
    {
        //
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
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

    public function getProducts(): ?array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPaymentForm(): ?string
    {
        return $this->paymentForm;
    }

    public function setPaymentForm(string $paymentForm): self
    {
        $this->paymentForm = $paymentForm;

        return $this;
    }

    public function getBookIds(): ?array
    {
        return array_keys($this->products);
    }

    public function getBookAmmount($id): ?int
    {
        if (!isset($this->products[$id]) || !is_integer($this->products[$id])) {
            return null;
        }

        return $this->products[$id];
    }
}
