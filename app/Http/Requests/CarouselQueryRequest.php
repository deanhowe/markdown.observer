<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarouselQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:0', 'max:200'],
            // We sanitize these in prepareForValidation to avoid hard validation failures
            'order_by' => ['nullable', 'string'],
            'direction' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            // Support both snake_case and camelCase for include_readme
            'include_readme' => ['nullable', 'boolean'],
            'includeReadme' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Provide sensible defaults and normalize keys
        $defaults = [
            'limit' => 12,
            'order_by' => 'rank',
            'direction' => 'asc',
            'type' => 'all',
            'include_readme' => false,
        ];

        $data = $this->all();

        // If camelCase includeReadme is provided, copy to snake_case
        if ($this->has('includeReadme') && ! $this->has('include_readme')) {
            $data['include_readme'] = filter_var($this->input('includeReadme'), FILTER_VALIDATE_BOOLEAN);
        }

        foreach ($defaults as $key => $value) {
            if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                $data[$key] = $value;
            }
        }

        // Sanitize values to allowed sets to avoid validation redirects and ensure graceful behavior
        $allowedOrderBy = ['rank', 'name', 'usage_count', 'type'];
        $allowedDirection = ['asc', 'desc'];
        $allowedType = ['all', 'prod', 'dev'];

        if (isset($data['order_by'])) {
            $orderBy = strtolower((string) $data['order_by']);
            if (! in_array($orderBy, $allowedOrderBy, true)) {
                $orderBy = 'rank';
            }
            $data['order_by'] = $orderBy;
        }

        if (isset($data['direction'])) {
            $direction = strtolower((string) $data['direction']);
            if (! in_array($direction, $allowedDirection, true)) {
                $direction = 'asc';
            }
            $data['direction'] = $direction;
        }

        if (isset($data['type'])) {
            $type = strtolower((string) $data['type']);
            if (! in_array($type, $allowedType, true)) {
                $type = 'all';
            }
            $data['type'] = $type;
        }

        // Normalize limit to int and clamp
        if (isset($data['limit'])) {
            $limit = (int) $data['limit'];
            if ($limit < 0) {
                $limit = 0;
            }
            if ($limit > 200) {
                $limit = 200;
            }
            $data['limit'] = $limit;
        }

        $this->merge($data);
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Ensure include_readme is present in validated data (may have come from includeReadme)
        if (! array_key_exists('include_readme', $validated)) {
            $validated['include_readme'] = filter_var($this->input('includeReadme', false), FILTER_VALIDATE_BOOLEAN);
        }

        return $validated;
    }
}
