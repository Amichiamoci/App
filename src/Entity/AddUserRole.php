<?php

namespace App\Entity;

class AddUserRole
{
    public ?string $User = null;
    public function hasUser(): bool { return $this->User !== null && trim(string: $this->User) !== ''; }
    public function getUser(): ?string { return $this->User; }

    public string $Role;
    public function getRole(): string { return $this->Role; }

    public function __construct(
        string $role,
        ?string $user = null,
    ) {
        $this->User = $user;
        $this->Role = $role;
    }
}