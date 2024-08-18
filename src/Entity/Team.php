<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Ignore;

class Team
{
    public string $Name;
    public int $Id;

    public string $SportName;
    public int $SportId;

    #[Ignore]
    public array $Members = [];
}