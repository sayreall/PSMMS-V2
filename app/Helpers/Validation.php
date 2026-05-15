<?php

namespace App\Helpers;

class Validation
{
    private array $errors = [];
    private array $data = [];

    public function validate(array $data, array $rules): self
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return $this;
    }

    private function applyRule(string $field, $value, string $rule): void
    {
        $param = null;
        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->errors[$field][] = ucfirst($field) . ' is required.';
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = 'Please enter a valid email address.';
                }
                break;

            case 'min':
                if (!empty($value) && is_string($value) && strlen($value) < (int)$param) {
                    $this->errors[$field][] = ucfirst($field) . " must be at least $param characters.";
                }
                break;

            case 'max':
                if (!empty($value) && is_string($value) && strlen($value) > (int)$param) {
                    $this->errors[$field][] = ucfirst($field) . " must not exceed $param characters.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a number.';
                }
                break;

            case 'integer':
                if (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->errors[$field][] = ucfirst($field) . ' must be an integer.';
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmValue = $this->data[$confirmField] ?? null;
                if ($value !== $confirmValue) {
                    $this->errors[$field][] = 'The ' . $field . ' confirmation does not match.';
                }
                break;

            case 'unique':
                if (!empty($value)) {
                    [$table, $column] = explode('.', $param, 2);
                    $db = \App\Config\Database::getInstance();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM `$table` WHERE `$column` = :value");
                    $stmt->execute([':value' => $value]);
                    if ($stmt->fetchColumn() > 0) {
                        $this->errors[$field][] = ucfirst($field) . ' already exists.';
                    }
                }
                break;

            case 'exists':
                if (!empty($value)) {
                    [$table, $column] = explode('.', $param, 2);
                    $db = \App\Config\Database::getInstance();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM `$table` WHERE `$column` = :value");
                    $stmt->execute([':value' => $value]);
                    if ($stmt->fetchColumn() == 0) {
                        $this->errors[$field][] = 'Selected ' . ucfirst($field) . ' is invalid.';
                    }
                }
                break;

            case 'same':
                if (!empty($value) && $value !== ($this->data[$param] ?? null)) {
                    $this->errors[$field][] = 'The ' . $field . ' and ' . $param . ' must match.';
                }
                break;

            case 'regex':
                if (!empty($value) && !preg_match($param, $value)) {
                    $this->errors[$field][] = 'The ' . $field . ' format is invalid.';
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, explode(',', $param))) {
                    $this->errors[$field][] = 'The selected ' . $field . ' is invalid.';
                }
                break;

            case 'file':
                if (empty($value) || !isset($value['error']) || $value['error'] !== UPLOAD_ERR_OK) {
                    $this->errors[$field][] = 'A valid file is required.';
                }
                break;

            case 'mimes':
                if (!empty($value) && isset($value['type'])) {
                    $allowed = explode(',', $param);
                    if (!in_array($value['type'], $allowed)) {
                        $this->errors[$field][] = 'File must be one of: ' . $param;
                    }
                }
                break;
        }
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function all(): array
    {
        return $this->errors;
    }

    public static function jsonResponse(array $errors, int $code = 422): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['errors' => $errors, 'success' => false]);
        exit;
    }
}