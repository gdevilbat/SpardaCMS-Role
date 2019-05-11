<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->text('access_scope');
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('role_id');
            $table->timestamps();
        });

        Schema::table('access_roles', function($table){
            $table->foreign('module_id')->references('id')->on('module')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_roles');
    }
}
