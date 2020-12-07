<?php declare(strict_types = 1);

namespace App\Foundation\Request;

use Countable;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class Validator
{
    private MessageBag $messages;
    private array $data;
    private array $rules;
    private string $currentRule;
    private array $failedRules = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        $this->messages = new MessageBag;

        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);
            }
        }

        return $this->messages->isEmpty();
    }

    protected function validateAttribute(string $attribute, string $rule): void
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = $this->parseStringRule($rule);

        if ($rule == '') {
            return;
        }

        $value = $this->data[$attribute];
        $method = "validate{$rule}";

        if ($this->isValidatable($method) && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->messages->add($attribute,  $rule);

            $this->failedRules[$attribute][$rule] = $parameters;
        }
    }

    protected static function parseStringRule($rules): array
    {
        $parameters = [];

        if (str_contains($rules, ':')) {
            [$rules, $parameter] = explode(':', $rules, 2);

            $parameters = str_getcsv($parameter);
        }

        return [Str::studly(trim($rules)), $parameters];
    }

    public function validateRequired(mixed $attribute, mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        }

        return true;
    }

    public function validateEmail(mixed $attribute, mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone(mixed $attribute, mixed $value): bool
    {
        $regex = '/(((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))/';
        return preg_match($regex, $value) !== false;
    }

    public function isValidatable($method): bool
    {
        return method_exists($this, $method);
    }
}
