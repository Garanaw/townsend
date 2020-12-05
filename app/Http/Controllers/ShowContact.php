<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ShowContact
{
    public function __invoke(): string
    {
        return 'contactForm.php';
    }
}
