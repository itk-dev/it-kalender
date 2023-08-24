<?php

namespace App\ICS;

enum BusyStatus: string
{
    case OutOfOffice = 'OOF';
    case WorkingElsewhere = 'WORKINGELSEWHERE';

    public function displayName(): string
    {
        return match ($this) {
            self::OutOfOffice => 'Away',
            self::WorkingElsewhere => 'Working elsewhere',
        };
    }
}
