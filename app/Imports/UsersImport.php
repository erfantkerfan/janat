<?php

namespace App\Imports;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        return new User([
            'f_name'       => $row['f_name'],
            'l_name'       => $row['l_name'],
            'father_name'  => $row['father_name'],
            'SSN'          => $row['ssn'],
            'staff_code'   => $row['staff_code'],
//            'salary'       => $row['salary'],
            'phone'        => $row['phone'],
            'mobile'       => $row['mobile'],
//            'email'        => $row['email'],
//            'address'      => $row['address'],
//            'description'  => $row['description'],
            'password'     => Hash::make($row['password']),
            'user_type_id' => 1,
            'status_id'    => 1
        ]);
    }

    public function rules(): array
    {
        return [

            'f_name' => ['required', 'string'],
            'l_name' => ['required', 'string'],
            'father_name' => ['sometimes', 'required', 'string'],
            'ssn' => ['required', 'unique:users,SSN'],
            'staff_code' => ['sometimes', 'nullable', 'string', 'unique:users,staff_code'],
//            'salary' => ['sometimes', 'nullable', 'integer'],
//            'address' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable'],
            'mobile' => ['sometimes', 'required', 'unique:users,mobile'],
//            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email'],
//            'description' => ['sometimes', 'nullable', 'string'],



//            'email' => Rule::in(['patrick@maatwebsite.nl']),
//
//            // Above is alias for as it always validates in batches
//            '*.email' => Rule::in(['patrick@maatwebsite.nl']),
//
//            '1' => Rule::in(['patrick@maatwebsite.nl']),
//
//            // Above is alias for as it always validates in batches
//            '*.1' => Rule::in(['patrick@maatwebsite.nl']),
//
//            // Can also use callback validation rules
//            '0' => function($attribute, $value, $onFailure) {
//                if ($value !== 'Patrick Brouwers') {
//                    $onFailure('Name is not Patrick Brouwers');
//                }
//            }
        ];
    }


    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '1.in' => 'Custom message for :attribute.',
        ];
    }
}
