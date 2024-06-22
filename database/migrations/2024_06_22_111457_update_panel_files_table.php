<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panel_files', static function (Blueprint $table) {
            // TODO The best way would be to replace the key on 'panel_id' with one on 'panel_deployment_id' but changing keys is tricky in SQLite
            $table->string('deployment_id')->after('crud_field_id');
        });
    }

    public function down(): void
    {
        Schema::table('panel_files', static function (Blueprint $table) {
            $table->dropColumn('deployment_id');
        });
    }
};
