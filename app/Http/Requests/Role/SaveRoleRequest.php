<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="SaveRoleRequest",
 *     required={"name", "permissions"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="admin"),
 *     @OA\Property(property="description", type="string", maxLength=500, example="Administrator role with full access"),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         @OA\Items(type="string", example="create_users")
 *     ),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="guard_name", type="string", example="web")
 * )
 */
class SaveRoleRequest extends FormRequest
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
        $roleId = $this->route('role') ?? null;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'description'   => ['sometimes', 'nullable', 'string', 'max:500'],
            'permissions'   => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
            'active'        => ['sometimes', 'boolean'],
            'guard_name'    => ['sometimes', 'string', 'in:web,api'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'The role name is required',
            'name.string'           => 'The role name must be a string',
            'name.max'              => 'The role name must not exceed 255 characters',
            'name.unique'           => 'A role with this name already exists',
            'description.string'    => 'The description must be a string',
            'description.max'       => 'The description must not exceed 500 characters',
            'permissions.required'  => 'At least one permission is required',
            'permissions.array'     => 'Permissions must be an array',
            'permissions.min'       => 'At least one permission must be provided',
            'permissions.*.required' => 'Each permission is required',
            'permissions.*.string'  => 'Each permission must be a string',
            'permissions.*.exists'  => 'One or more permissions do not exist',
            'active.boolean'        => 'Active status must be true or false',
            'guard_name.string'     => 'Guard name must be a string',
            'guard_name.in'         => 'Guard name must be either web or api',
        ];
    }
}
