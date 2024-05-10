<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->ulid('uuid')->primary();
            $table->ulid('payer_id');
            $table->foreign('payer_id')->references('uuid')->on('users');
            $table->ulid('payee_id');
            $table->foreign('payee_id')->references('uuid')->on('users');
            $table->decimal('value', 10, 2)->unsigned();
            $table->boolean('authorized')->default(0);
            $table->boolean('notification_sent')->default(0);
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
}
