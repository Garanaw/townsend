<?php declare(strict_types = 1);

namespace App\Foundation\Request;

use App\Foundation\Container;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use UnexpectedValueException;

class Request extends BaseRequest
{
    public static function createFromGlobals(): self
    {
        $request = parent::createFromGlobals();
        $request->validateToken();

        return $request;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
            ],
            'phone' => [
                'required',
                'phone',
            ],
            'email' => [
                'required',
                'email',
            ],
            'message' => [
                'required',
                'min:25',
            ],
        ];
    }

    protected function validateToken()
    {
        if ($this->getMethod() !== 'POST') {
            return;
        }

        if (!$token = $this->get('_token', null)) {
            throw new UnexpectedValueException('No token passed');
        }

        if ($token !== Container::getInstance()->make('session', ['debug' => true])->token()) {
            throw new UnexpectedValueException('The token does not match');
        }
    }
}
