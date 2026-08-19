<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $guestId = DB::table('roles')->where('name', 'guest')->value('id');
        $userId = DB::table('roles')->where('name', 'user')->value('id');
        $normalId = DB::table('roles')->where('name', 'normal')->value('id');

        if ($guestId && ($userId || $normalId)) {
            $replaceId = $guestId;
            if ($userId) {
                DB::table('model_has_roles')->where('role_id', $userId)->update(['role_id' => $replaceId]);
            }
            if ($normalId) {
                DB::table('model_has_roles')->where('role_id', $normalId)->update(['role_id' => $replaceId]);
            }
            if ($userId) {
                DB::table('roles')->where('id', $userId)->delete();
            }
            if ($normalId) {
                DB::table('roles')->where('id', $normalId)->delete();
            }
        } elseif ($userId && $normalId) {
            $replaceId = $userId;
            DB::table('model_has_roles')->where('role_id', $normalId)->update(['role_id' => $replaceId]);
            DB::table('roles')->where('id', $normalId)->delete();
            DB::table('roles')->where('id', $userId)->update(['name' => 'guest']);
        } elseif ($userId) {
            DB::table('roles')->where('id', $userId)->update(['name' => 'guest']);
        } elseif ($normalId) {
            DB::table('roles')->where('id', $normalId)->update(['name' => 'guest']);
        }

        DB::table('roles')->where('name', 'super-admin')->update(['name' => 'superadmin']);
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'superadmin')->update(['name' => 'super-admin']);
        DB::table('roles')->where('name', 'guest')->update(['name' => 'normal']);
    }
};
