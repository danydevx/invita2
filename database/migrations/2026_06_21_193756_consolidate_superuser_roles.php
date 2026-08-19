<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $superuserId = DB::table('roles')->where('name', 'superuser')->value('id');
        $superadminId = DB::table('roles')->where('name', 'superadmin')->value('id');

        if ($superuserId) {
            DB::table('model_has_roles')->where('role_id', $superuserId)->update(['role_id' => $superadminId]);
            DB::table('role_has_permissions')->where('role_id', $superuserId)->delete();
            DB::table('roles')->where('id', $superuserId)->delete();
        }
    }

    public function down(): void
    {
        $exists = DB::table('roles')->where('name', 'superadmin')->exists();
        if (!$exists) {
            DB::table('roles')->insert(['name' => 'superadmin', 'guard_name' => 'web']);
        }
    }
};
