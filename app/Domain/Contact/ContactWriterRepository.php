<?php declare(strict_types = 1);

namespace App\Domain\Contact;

class ContactWriterRepository
{
    public function store(User $user): User
    {
        $user->save();
        var_dump('saved user');

        $user->getMessages()->each(
            static fn (Message $message) => $message->setUserId($user->getId())->save()
        );

        return $user;
    }
}
