<?php declare(strict_types = 1);

namespace App\Domain\Contact;

use App\Foundation\Database\Model;
use Carbon\Carbon;

class Message extends Model
{
    protected array $fillable = [
        'user_id' => 'int',
        'message' => 'string',
        'sent_at' => 'carbon',
        'ip'      => 'string',
    ];

    protected int $user_id;
    protected string $message;
    protected Carbon|string $sent_at;
    protected string $ip;

    public function setUserId(int $userId): self
    {
        $this->user_id = $userId;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setSentAt(Carbon|string $date): self
    {
        $this->sent_at = Carbon::parse($date);
        return $this;
    }

    public function isSentAt(): Carbon
    {
        return Carbon::parse($this->sent_at);
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
