<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Domain\Contact\ContactWriterService as Writer;
use App\Domain\Contact\Message;
use App\Domain\Contact\User;
use App\Domain\Mail\MailService as Mail;
use App\Foundation\Request\Request;
use App\Foundation\Request\Validator;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProcessContact
{
    private Request $request;
    private Writer $writer;
    private Mail $mail;

    public function __construct(Request $request, Writer $writer, Mail $mail)
    {
        $this->request = $request;
        $this->writer = $writer;
        $this->mail = $mail;
    }

    public function __invoke(): RedirectResponse
    {
        if ($this->validate($this->request) === false) {
            var_dump('pues NO!!');
            exit(1);
        }

        $user = (new User())->first(['email' => $this->request->get('email')])
            ?? User::make($this->request->request->all());
        $message = Message::make([
            'message' => strip_tags($this->request->get('message')),
            'sent_at' => Carbon::now(),
            'ip' => $this->request->getClientIp(),
        ]);
        $user->setMessages([$message]);

        $user = $this->writer->store($user);
        $this->notifyAdmin($user);
        return new RedirectResponse('/contact');
    }

    private function notifyAdmin(User $user): bool
    {
        return $this->mail->subject('New contact: ')
            ->prepareMessage($user)
            ->send();
    }

    private function validate(Request $request): bool
    {
        $validator = new Validator($request->request->all(), $request->rules());
        return $validator->validate();
    }
}
