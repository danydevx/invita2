<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKeys = DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'business_clients')
            ->whereIn('column_name', ['appointment_date', 'start_time', 'end_time'])
            ->get();

        foreach ($foreignKeys as $foreignKey) {
            $constraintName = $foreignKey->constraint_name ?? null;
            if ($constraintName) {
                Schema::table('business_clients', function (Blueprint $table) use ($constraintName) {
                    $table->dropForeign($constraintName);
                });
            }
        }

        Schema::table('business_clients', function (Blueprint $table) {
            $table->index('business_id', 'business_clients_business_id_foreign');
            $table->dropIndex(['business_id', 'appointment_date']);
            $table->dropColumn(['appointment_date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::table('business_clients', function (Blueprint $table) {
            $table->dropIndex('business_clients_business_id_foreign');
            $table->date('appointment_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->index(['business_id', 'appointment_date']);
        });
    }
};
