<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->string('password');
            $table->enum('level', ['admin', 'kasir'])->default('kasir');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletesTz();
        });
        DB::table('users')->insert([
            ['name' => 'Administrator', 'username' => 'administrator', 'password' => Hash::make('halo1234'), 'level' => 'admin'],
            ['name' => 'Kasir','username' => 'kasir', 'password' => Hash::make('halo4321'), 'level' => 'kasir'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
