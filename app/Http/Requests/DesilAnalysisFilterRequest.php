<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesilAnalysisFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_year' => ['nullable', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'end_year' => ['nullable', 'integer', 'min:2000', 'max:'.(now()->year + 1), 'gte:start_year'],
            'hamlet_id' => ['nullable', 'integer', 'exists:village_hamlets,id'],
            'decile' => ['nullable', 'in:D1,D2,D3,D4,D5'],
            'head_gender' => ['nullable', 'in:laki_laki,perempuan'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('start_year') && ! $this->filled('end_year')) {
            $this->merge(['end_year' => $this->input('start_year')]);
        }
        if (! $this->filled('start_year') && $this->filled('end_year')) {
            $this->merge(['start_year' => $this->input('end_year')]);
        }
    }
}
