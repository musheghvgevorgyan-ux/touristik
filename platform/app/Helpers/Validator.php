<?php

namespace App\Helpers;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'rule' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $error = $this->$method($field, $value, $params);
                    if ($error) {
                        $this->errors[$field] = $error;
                        break; // One error per field
                    }
                }
            }
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    // ─── Rule Methods ────────────────────────────────────

    private function ruleRequired(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === []) {
            return $this->label($field) . ' is required.';
        }
        return null;
    }

    private function ruleEmail(string $field, mixed $value): ?string
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->label($field) . ' must be a valid email address.';
        }
        return null;
    }

    private function ruleMin(string $field, mixed $value, array $params): ?string
    {
        $min = (int) ($params[0] ?? 0);
        if ($value && strlen($value) < $min) {
            return $this->label($field) . " must be at least {$min} characters.";
        }
        return null;
    }

    private function ruleMax(string $field, mixed $value, array $params): ?string
    {
        $max = (int) ($params[0] ?? 255);
        if ($value && strlen($value) > $max) {
            return $this->label($field) . " must not exceed {$max} characters.";
        }
        return null;
    }

    private function ruleConfirmed(string $field, mixed $value): ?string
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;
        if ($value !== $confirmValue) {
            return $this->label($field) . ' confirmation does not match.';
        }
        return null;
    }

    private function ruleNumeric(string $field, mixed $value): ?string
    {
        if ($value && !is_numeric($value)) {
            return $this->label($field) . ' must be a number.';
        }
        return null;
    }

    private function rulePhone(string $field, mixed $value): ?string
    {
        if ($value && !preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $value)) {
            return $this->label($field) . ' must be a valid phone number.';
        }
        return null;
    }

    private function ruleIn(string $field, mixed $value, array $params): ?string
    {
        if ($value && !in_array($value, $params, true)) {
            return $this->label($field) . ' is not a valid option.';
        }
        return null;
    }

    private function ruleUnique(string $field, mixed $value, array $params): ?string
    {
        if (!$value) return null;

        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        $db = \Core\Database::getInstance();
        $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
        $bindings = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $bindings[] = $exceptId;
        }

        $result = $db->query($sql, $bindings)->fetch();
        if ($result['cnt'] > 0) {
            return $this->label($field) . ' is already taken.';
        }
        return null;
    }

    private function ruleDate(string $field, mixed $value): ?string
    {
        if ($value && !strtotime($value)) {
            return $this->label($field) . ' must be a valid date.';
        }
        return null;
    }

    /**
     * Convert field_name to "Field name"
     */
    private function label(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }
}
