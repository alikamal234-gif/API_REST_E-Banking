<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();

            $table->enum('type', ['courant', 'epargne', 'mineur']);

            $table->decimal('balance', 15, 2)->default(0);

            $table->decimal('overdraft_limit', 15, 2)->nullable(); 
            $table->decimal('interest_rate', 5, 2)->nullable(); 

            $table->enum('status', ['active', 'blocked', 'closed'])->default('active');
            $table->string('blocked_reason')->nullable();

            $table->integer('monthly_withdrawals')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
