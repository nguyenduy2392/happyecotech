<?php

namespace App\Console;

use Illuminate\Foundation\Console\ServeCommand;

class LocalServeCommand extends ServeCommand
{
    protected function serverCommand()
    {
        $command = parent::serverCommand();
        $command[array_key_last($command)] = __DIR__.'/local-router.php';

        return $command;
    }
}
