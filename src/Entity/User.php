<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;




#[Table(name: "user")]
#[InheritanceType("JOINED")]
#[DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "user" => User::class,
    "coach" => Coach::class,
    "kids" => Kids::class
])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'amisAvecMoi')]
    #[ORM\JoinTable(name: 'user_amis')]
    private Collection $mesAmis;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'mesAmis')]
    private Collection $amisAvecMoi;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'recipient')]
    private Collection $receivedMessages;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->mesAmis = new ArrayCollection();
        $this->amisAvecMoi = new ArrayCollection();
        $this->recipient = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function getMesAmis(): Collection
    {
        return $this->mesAmis;
    }

    public function addAmi(User $ami): static
    {
        if (!$this->mesAmis->contains($ami)) {
            $this->mesAmis->add($ami);
            $ami->amisAvecMoi[] = $this;
        }

        return $this;
    }

    public function removeAmi(User $ami): static
    {
        if ($this->mesAmis->removeElement($ami)) {
            $ami->amisAvecMoi->removeElement($this);
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getRecipient(): Collection
    {
        return $this->recipient;
    }

    public function addRecipient(Message $recipient): static
    {
        if (!$this->recipient->contains($recipient)) {
            $this->recipient->add($recipient);
            $recipient->setSender($this);
        }

        return $this;
    }

    public function removeRecipient(Message $recipient): static
    {
        if ($this->recipient->removeElement($recipient)) {
            // set the owning side to null (unless already changed)
            if ($recipient->getSender() === $this) {
                $recipient->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $receivedMessage): static
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages->add($receivedMessage);
            $receivedMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $receivedMessage): static
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getRecipient() === $this) {
                $receivedMessage->setRecipient(null);
            }
        }

        return $this;
    }
}
