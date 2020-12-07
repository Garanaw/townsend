<?php declare(strict_types = 1);

namespace App\Domain\Contact;

use App\Domain\Contact\ContactWriterRepository as Writer;

class ContactWriterService
{
    private Writer $writer;

    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    public function store(User $user): User
    {
        return $this->writer->store($user);
    }
}
