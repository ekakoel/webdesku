<?php

namespace App\Http\Requests;

use App\Models\VillageHouseholdWelfare;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVillageHouseholdWelfareRequest extends FormRequest
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
            'year' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'reference_code' => ['required', 'string', 'max:80', Rule::unique('village_household_welfares')->where('village_id', $this->route('village_household_welfare')->village_id)->where('year', $this->input('year'))->ignore($this->route('village_household_welfare'))],
            'village_hamlet_id' => ['nullable', 'integer', Rule::exists('village_hamlets', 'id')->where('village_id', $this->route('village_household_welfare')->village_id)],
            'decile' => ['nullable', Rule::in(VillageHouseholdWelfare::DECILES)],
            'head_gender' => ['nullable', Rule::in(array_keys(VillageHouseholdWelfare::GENDERS))],
            'is_outside_village' => ['nullable', 'boolean'],
            'requires_verification' => ['nullable', 'boolean'],
            'source' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
