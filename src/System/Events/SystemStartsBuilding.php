<?php

namespace MorningTrain\Laravel\Dev\Commands\System\Events;

use Illuminate\Console\Command;

class SystemStartsBuilding
{

    public $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

}
