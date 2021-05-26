<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugTicketDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bug_ticket_details', function (Blueprint $table) {
            $table->id();
            $table->longText('comment');
            $table->foreignId('user_id');
            $table->foreignId('bug_ticket_id');
            $table->foreignId('bug_ticket_status_id');
            $table->softDeletes();
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
        Schema::dropIfExists('bug_ticket_details');
    }
}
