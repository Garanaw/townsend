<?php declare(strict_types = 1);

namespace App\Domain\Contact;

use App\Foundation\Database\Model;
use Illuminate\Support\Collection;

class User extends Model
{
    protected array $fillable = [
        'name'       => 'string',
        'email'      => 'string',
        'phone'      => 'string',
        'newsletter' => 'bool',
    ];

    protected string $name;
    protected string $email;
    protected string $phone;
    protected bool $newsletter = false;

    protected ?Collection $messages = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    public function setMessages(Collection|array $messages): self
    {
        $this->messages = new Collection($messages);
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages ?: new Collection();
    }
}
