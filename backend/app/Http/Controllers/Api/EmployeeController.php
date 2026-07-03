<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(
            User::where('role', 'employee')->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $employee = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'employee',
        ]);

        return response()->json($employee, 201);
    }

    public function update(Request $request, User $employee)
    {
        abort_if($employee->role !== 'employee', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$employee->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $employee->name = $data['name'];
        $employee->email = $data['email'];
        if (! empty($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }
        $employee->save();

        return response()->json($employee);
    }

    public function destroy(User $employee)
    {
        abort_if($employee->role !== 'employee', 404);

        $employee->delete();

        return response()->json(['message' => 'Employee deleted.']);
    }
}
