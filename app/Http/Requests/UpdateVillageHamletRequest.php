<?php

namespace App\Http\Requests;

use App\Models\VillageHamlet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateVillageHamletRequest extends FormRequest
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
        $hamlet = $this->route('village_hamlet');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                function (string $attribute, mixed $value, \Closure $fail) use ($hamlet): void {
                    if ($hamlet && VillageHamlet::query()->where('village_id', $hamlet->village_id)->where('normalized_name', Str::upper((string) $value))->whereKeyNot($hamlet->getKey())->exists()) {
                        $fail('Nama Banjar sudah digunakan.');
                    }
                },
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => Str::squish((string) $this->input('name'))]);
    }
}
