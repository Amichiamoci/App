<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Esiste già un account con questa email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ADMIN = 'ROLE_ADMIN';
    const USER = 'ROLE_USER';
    const REFEREE = 'ROLE_REFEREE';
    const EXTERNAL_PROVIDER = 'ROLE_EXTERNAL_PROVIDER';

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

    #[ORM\Column(length: 256)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $isVerified = false;

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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::USER;

        // Detect if the user is admin from the environment
        if (
            in_array(needle: 'ADMIN_USER_EMAIL', haystack: $_ENV) && 
            is_string(value: $_ENV['ADMIN_USER_EMAIL']) &&
            $this->email === $_ENV['ADMIN_USER_EMAIL']
        ) {
            $roles[] = self::ADMIN;
        }
        
        return array_unique(array: $roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        if (in_array(needle: $role, haystack: $this->roles))
        {
            return $this;
        }

        $this->roles[] = $role;
        return $this;
    }

    public function removeRole(string $role): static
    {
        $this->roles = array_filter(array: $this->roles, callback: function (string $r) use($role): bool {
            return $r !== $role;
        });
        return $this;
    }

    /**
     * Checks if the user has the given role
     * If the user has an ADMIN role the function will return true regardless
     * @param string $role The role to check
     * @return bool
     */
    public function isInRole(string $role): bool
    {
        $roles = $this->getRoles();
        return in_array(needle: self::ADMIN, haystack: $roles) || in_array(needle: $role, haystack: $roles);
    }
    public function isAdmin(): bool
    {
        return in_array(needle: self::ADMIN, haystack: $this->getRoles());
    }
    public function isReferee(): bool
    {
        return $this->isInRole(role: self::REFEREE);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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


    private const PASSWORD_GEN_MIN = 8;
    private const PASSWORD_GEN_MAX = 128;
    private const PASSWORD_LETTERS_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const PASSWORD_DIGITS = '1234567890';
    private const PASSWORD_SYMBOLS = '!£$€%&/()?=^[]#';
    public static function RandomPassword(int $length): string
    {
        if ($length < self::PASSWORD_GEN_MIN || $length > self::PASSWORD_GEN_MAX)
        {
            throw new \LengthException(
                message: '$length of password must be >= ' . 
                self::PASSWORD_GEN_MIN . 
                ' and <= ' . 
                self::PASSWORD_GEN_MAX .
                '. ' . 
                $length .
                ' given'
            );
        }

        $alphabet = 
            self::PASSWORD_LETTERS_UPPER . 
            strtolower(string: self::PASSWORD_LETTERS_UPPER) .
            self::PASSWORD_DIGITS .
            self::PASSWORD_SYMBOLS; 

        $pass = array();
        for ($i = 0; $i < $length; $i++)
        {
            $pass[] = $alphabet[random_int(min: 0, max: strlen(string: $alphabet) - 1)];
        }
        return join(array: $pass, separator: '');
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
