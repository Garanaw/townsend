<?php declare(strict_types = 1);

namespace App\Http\Controllers;

class ShowContact
{
    public function __invoke()
    {
        var_dump([
            'aquí' => 'estoy'
        ]);
    }
}
