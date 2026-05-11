<?php

namespace App\Services;

use App\Models\Scheme;

/**
 * Server-side JSON eligibility rule evaluator.
 *
 * Each rule in a scheme has the shape:
 *   { "field": "age", "operator": ">=", "value": 18, "label": "Must be 18+" }
 *
 * Supported operators: >=  <=  ==  !=  in  not_in
 */
class EligibilityService
{
    /**
     * Evaluate all rules for a scheme against applicant data.
     *
     * @param Scheme $scheme
     * @param array  $applicantData  Key-value pairs: ['age' => 22, 'state' => 'UP', ...]
     * @return array{eligible: bool, passed: array, failed: array, missing: array}
     */
    public function evaluate(Scheme $scheme, array $applicantData): array
    {
        $rules   = $scheme->eligibility_rules ?? [];
        $passed  = [];
        $failed  = [];
        $missing = [];

        foreach ($rules as $rule) {
            $field    = $rule['field']    ?? null;
            $operator = $rule['operator'] ?? null;
            $expected = $rule['value']    ?? null;
            $label    = $rule['label']    ?? $field;

            if (! $field || ! $operator) {
                continue;
            }

            // If the applicant hasn't provided this field, mark as missing
            if (! array_key_exists($field, $applicantData)) {
                $missing[] = ['field' => $field, 'label' => $label];
                continue;
            }

            $actual = $applicantData[$field];
            $result = $this->applyOperator($actual, $operator, $expected);

            $entry = [
                'field'    => $field,
                'label'    => $label,
                'operator' => $operator,
                'expected' => $expected,
                'actual'   => $actual,
            ];

            if ($result) {
                $passed[] = $entry;
            } else {
                $failed[] = $entry;
            }
        }

        return [
            'eligible' => empty($failed) && empty($missing),
            'passed'   => $passed,
            'failed'   => $failed,
            'missing'  => $missing,
        ];
    }

    /**
     * Apply a single comparison operator.
     */
    private function applyOperator(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '>='     => (float) $actual >= (float) $expected,
            '<='     => (float) $actual <= (float) $expected,
            '>'      => (float) $actual >  (float) $expected,
            '<'      => (float) $actual <  (float) $expected,
            '=='     => $actual == $expected,
            '!='     => $actual != $expected,
            'in'     => is_array($expected) && in_array($actual, $expected),
            'not_in' => is_array($expected) && ! in_array($actual, $expected),
            default  => false,
        };
    }
}
