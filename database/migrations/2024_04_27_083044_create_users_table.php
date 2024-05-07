<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @codeCoverageIgnore
 */
return new class () extends Migration {
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'sqlite';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_login', 100)->unique();
            $table->string('password', 255);
            $table->foreignId('user__emp_id')->constrained('employees', 'emp_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user__pro_id')->constrained('profiles', 'pro_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->tinyInteger('user_state')->default(1);
            $table->string('user_photo', 100)->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
