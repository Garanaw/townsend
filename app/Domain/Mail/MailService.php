<?php declare(strict_types = 1);

namespace App\Domain\Mail;

use App\Domain\Contact\Message;
use App\Domain\Contact\User;
use App\Foundation\Config\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Throwable;

class MailService
{
    private const MAX_CHARACTERS_PER_LINE = 70;

    private Config $config;
    private PHPMailer $mailer;
    private string $from;
    private string $to;
    private string $subject;
    private string $message;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->mailer = $this->getMailer();
        $this->from($config->get('ADMIN_EMAIL'));
        $this->addRecipient($config->get('ADMIN_EMAIL'));
    }

    private function getMailer(): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = $this->config->get('MAIL_ENCRYPTION');
        $mailer->Host = gethostbyname($this->config->get('MAIL_ENCRYPTION') . '://' . $this->config->get('MAIL_HOST'));
        $mailer->Port = $this->config->get('MAIL_PORT');
        $mailer->Username = $this->config->get('MAIL_USERNAME');
        return $mailer;
    }

    public function from(string $from): self
    {
        $this->mailer->setFrom($from);
        return $this;
    }

    public function to(string $to): self
    {
        $this->mailer->addAddress($to);
        return $this;
    }

    public function addRecipient(string $recipient): self
    {
        return $this->to($recipient);
    }

    public function subject(string $subject): self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function message(string $message): self
    {
        $this->mailer->Body = wordwrap($message, self::MAX_CHARACTERS_PER_LINE);
        return $this;
    }

    public function prepareMessage(User $user): self
    {
        /** @var Message $contact */
        $contact = $user->getMessages()->first();
        $userName = $user->getName();
        $email = $user->getEmail();
        $query = $contact->getMessage();
        $ip = $contact->getIp();
        $date = $contact->isSentAt()->toDateTimeString();

        $message = "$userName ($email) has contacted you: \n$query\n (sent from ip ($ip) at $date)";

        return $this->message($message);
    }

    public function send(): bool
    {
        try {
            return $this->mailer->send();
        } catch (Throwable) {
            return false;
        }
//        return mail($this->to, $this->subject, $this->message, [
//            'From' => $this->from,
//        ]);
    }
}
