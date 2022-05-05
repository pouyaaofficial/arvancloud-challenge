<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactionables', function (Blueprint $table) {
            $table->foreignId('wallet_id')
            ->constrained()
            ->onDelete('cascade');

            $table->morphs('transactionable');

            $table->primary(['wallet_id', 'transactionable_id', 'transactionable_type'], 'id');
            $table->unique(['wallet_id', 'transactionable_id', 'transactionable_type'], 'transactionable_wallet');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
