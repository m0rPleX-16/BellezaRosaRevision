<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user() ? $this->user()->id : null;
        $user = $this->user();
        
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique(User::class)->ignore($userId),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\-\+\(\)\s]+$/',
            ],
            'gender' => ['nullable', 'in:male,female,other'],
        ];
        
        // Customer-specific fields
        if ($user && $user->isCustomer()) {
            $rules['birth_date'] = ['nullable', 'date', 'before:today'];
            $rules['notes'] = ['nullable', 'string', 'max:1000'];
        }
        
        // Staff-specific fields
        if ($user && $user->isStaff()) {
            $rules['color_code'] = ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'];
        }
        
        return $rules;
    }
}
