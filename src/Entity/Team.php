<?php

namespace App\Entity;

class Team
{
    public string $Name;
    public int $Id;

    public string $SportName;
    public int $SportId;
    
    public array $Members;
}