<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;

class ProcessContact
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke()
    {

    }
}
