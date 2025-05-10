<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class ApiError
{
    #[Assert\NotBlank]
    public string $Message;

    public array $Stack = [];

    #[Assert\Range(min: 0)]
    public int $Line;

    #[Assert\NotBlank]
    public string $File;

    public function BuildMessage(): string
    {
        return 
            $this->Message . PHP_EOL .
            $this->File . '#' . $this->Line
        ;
    }
}