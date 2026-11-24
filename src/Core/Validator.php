<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val === '') {
            $this->addError($field, "Майдони «{$label}» ҳатмист");
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val !== '' && mb_strlen($val, 'UTF-8') > $max) {
            $this->addError($field, "Майдони «{$label}» набояд аз {$max} аломат зиёд бошад");
        }
        return $this;
    }

    public function date(string $field, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val !== '') {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $val);
            if (!$dt || $dt->format('Y-m-d') !== $val) {
                $this->addError($field, "Санаи нодуруст дар майдони «{$label}» (СССС-ММ-РР)");
            }
        }
        return $this;
    }

    public function time(string $field, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $val)) {
            $this->addError($field, "Вақти нодуруст дар майдони «{$label}» (СС:ДД)");
        }
        return $this;
    }

    public function numericRange(string $field, float $min, float $max, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val !== '') {
            if (!is_numeric($val) || (float) $val < $min || (float) $val > $max) {
                $this->addError($field, "«{$label}» бояд аз {$min} то {$max} бошад");
            }
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        $val = $this->data[$field] ?? '';
        if ($val !== '' && !in_array($val, $allowed, false)) {
            $this->addError($field, "Қимати ноҷоиз барои «{$label}»");
        }
        return $this;
    }

    public function addError(string $field, string $message): self
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
