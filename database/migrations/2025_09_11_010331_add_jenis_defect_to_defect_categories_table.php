<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('defect_categories', function (Blueprint $table) {
            $table->integer('jenis_defect')->nullable()->after('defect_name');
        });
    }

    public function down(): void
    {
        Schema::table('defect_categories', function (Blueprint $table) {
            $table->dropColumn('jenis_defect');
        });
    }
};
