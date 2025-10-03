<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('defect_input_details', function (Blueprint $table) {
            $table->string('pica')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('defect_input_details', function (Blueprint $table) {
            $table->dropColumn('pica');
        });
    }
};
