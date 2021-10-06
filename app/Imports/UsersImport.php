<?php

namespace App\Imports;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
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
            'salary'       => $row['salary'],
            'phone'        => $row['phone'],
            'mobile'       => $row['mobile'],
            'email'        => $row['email'],
            'address'      => $row['address'],
            'description'  => $row['description'],
            'password'     => Hash::make($row['password']),
            'user_type_id' => 1,
            'status_id'    => 1
        ]);
    }
}
