<?php

namespace App\Services;

use App\Models\User;

class UserService extends BaseService
{
    public function get_users() {
        $users = User::get();
        return response()->json([
            'data' => $users
        ]);
    }

    public function update_user($data) {
        $user = User::findOrFail($data['id']);
        $user->update([
            'name' => $data['name'],
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    public function delete_user($data) {
        $user = User::findOrFail($data['id']);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}