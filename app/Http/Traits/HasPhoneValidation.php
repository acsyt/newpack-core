<?php

namespace App\Http\Traits;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

trait HasPhoneValidation
{
    private const SUPPORTED_REGIONS = ['MX', 'US'];

    protected function prepareForValidation()
    {
        if ($this->has('phone') && $this->input('phone')) {
            $raw = (string) $this->input('phone');
            $normalizedInput = $this->sanitizeRawInput($raw);
            $this->merge(['phone' => $normalizedInput]);
        }
    }

    protected function passedValidation(): void
    {
        if ($this->has('phone') && $this->validated('phone')) {
            $raw = (string) $this->validated('phone');
            $e164 = $this->toE164($raw, self::SUPPORTED_REGIONS);

            if ($e164 === null) {
                // Should not happen if validation passed, but good for safety
                abort(422, 'No se pudo normalizar el número telefónico.');
            }

            $this->merge(['phone' => $e164]);
        }
    }

    private function sanitizeRawInput(string $raw): string
    {
        $raw = trim($raw);
        $hasPlus = str_starts_with($raw, '+');
        $digitsOnly = preg_replace('/\D+/', '', $raw) ?? '';
        return $hasPlus ? '+' . $digitsOnly : $digitsOnly;
    }

    private function toE164(string $raw, array $regions): ?string
    {
        $util = PhoneNumberUtil::getInstance();

        if (str_starts_with($raw, '+')) {
            try {
                $proto = $util->parse($raw, null);
                return $util->isValidNumber($proto)
                    ? $util->format($proto, PhoneNumberFormat::E164)
                    : null;
            } catch (NumberParseException) {
                return null;
            }
        }

        foreach ($regions as $region) {
            try {
                $proto = $util->parse($raw, $region);
                if ($util->isValidNumber($proto)) {
                    return $util->format($proto, PhoneNumberFormat::E164);
                }
            } catch (NumberParseException) {
                continue;
            }
        }

        return null;
    }
}
