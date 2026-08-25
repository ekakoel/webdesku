<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatisticsPeriodRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $startYear = $this->input('start_year');
        $endYear = $this->input('end_year');
        $legacyYear = $this->input('year');

        if (! $startYear && ! $endYear && $legacyYear) {
            $this->merge([
                'start_year' => $legacyYear,
                'end_year' => $legacyYear,
            ]);

            return;
        }

        if ($startYear && ! $endYear) {
            $this->merge(['end_year' => $startYear]);
        }

        if (! $startYear && $endYear) {
            $this->merge(['start_year' => $endYear]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxYear = now()->year + 1;

        return [
            'start_year' => ['nullable', 'integer', 'min:2000', "max:{$maxYear}"],
            'end_year' => ['nullable', 'integer', 'min:2000', "max:{$maxYear}", 'gte:start_year'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_year.integer' => 'Dari Tahun harus berupa angka tahun.',
            'start_year.min' => 'Dari Tahun tidak valid.',
            'start_year.max' => 'Dari Tahun tidak boleh melebihi tahun depan.',
            'end_year.integer' => 'Sampai Tahun harus berupa angka tahun.',
            'end_year.min' => 'Sampai Tahun tidak valid.',
            'end_year.max' => 'Sampai Tahun tidak boleh melebihi tahun depan.',
            'end_year.gte' => 'Sampai Tahun harus sama atau setelah Dari Tahun.',
        ];
    }
}
