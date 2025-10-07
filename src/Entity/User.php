<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements \Symfony\Component\Security\Core\User\UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email obligatoire')]
    #[Assert\Email(message: 'Email invalide')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Mot de passe obligatoire')]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit faire au moins 8 caractères',
    )]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'Le mot de passe doit contenir au moins une lettre majuscule',
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'Le mot de passe doit contenir au moins un chiffre',
    )]
    #[Assert\Regex(
        pattern: '/[\W_]/',
        message: 'Le mot de passe doit contenir au moins un caractère spécial',
    )]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $last_login = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $last_email_sent = null;

    /** @var string[] */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTime $last_login): static
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getLastEmailSent(): ?\DateTime
    {
        return $this->last_email_sent;
    }

    public function setLastEmailSent(?\DateTime $last_email_sent): static
    {
        $this->last_email_sent = $last_email_sent;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, nettoie-les ici
    }
}
