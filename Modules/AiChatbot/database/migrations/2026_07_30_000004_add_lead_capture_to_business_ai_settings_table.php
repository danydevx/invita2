<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->boolean('lead_capture_enabled')->default(false)->after('rag_max_results');
            $table->string('lead_capture_trigger', 50)->default('after_3_messages')->after('lead_capture_enabled');
            $table->string('lead_capture_title', 200)->default('¿Te gustaría recibir noticias sobre nosotros?')->after('lead_capture_trigger');
            $table->string('lead_capture_description', 500)->default('Déjanos tu correo y te mantendremos informado.')->after('lead_capture_title');
        });
    }

    public function down(): void
    {
        Schema::table('business_ai_settings', function (Blueprint $table) {
            $table->dropColumn(['lead_capture_enabled', 'lead_capture_trigger', 'lead_capture_title', 'lead_capture_description']);
        });
    }
};
