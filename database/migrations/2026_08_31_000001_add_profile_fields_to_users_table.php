<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('email');
            $table->string('gender')->nullable()->after('profile_image');
            $table->unsignedTinyInteger('age')->nullable()->after('gender');

            $table->unsignedInteger('total_children')->default(0)->after('age');
            $table->unsignedInteger('total_daughters')->default(0)->after('total_children');
            $table->unsignedInteger('total_sons')->default(0)->after('total_daughters');
            $table->unsignedInteger('total_transgender')->default(0)->after('total_sons');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image', 'gender', 'age',
                'total_children', 'total_daughters', 'total_sons', 'total_transgender',
            ]);
        });
    }
};
