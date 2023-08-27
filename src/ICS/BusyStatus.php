<?php

namespace App\ICS;

enum BusyStatus: string
{
    case OutOfOffice = 'OOF';
    case WorkingElsewhere = 'WORKINGELSEWHERE';
    case Busy = 'BUSY';

    case TENTATIVE = 'TENTATIVE';

    case FREE = 'FREE';

    public function displayName(): string
    {
        return match ($this) {
            self::OutOfOffice => 'Out of office',
            self::WorkingElsewhere => 'Working elsewhere',
            self::Busy => 'Busy',
            self::TENTATIVE => 'Tentative',
            self::FREE => 'Free',
        };
    }
}
