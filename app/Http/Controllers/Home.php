<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Home
{
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/contact');
    }
}
