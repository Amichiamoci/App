<?php

namespace App\Entity;

class SportMatch
{
    public int $id;
    public int $tourneyId;
    public string $tourneyName;

    public int $sportId;
    public string $sportName;

    public int $year;

    // todo: add date handling
    public ?string $localeFormattedDate;

    public ?string $field;


    //
    // Teams and results
    //
    public string $homeTeam;
    public int $homeTeamId;
    public ?string $homeScore;


    public string $guestTeam;
    public int $guestTeamId;
    public ?string $guestScore;
}
