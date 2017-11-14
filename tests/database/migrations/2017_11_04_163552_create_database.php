<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


/**
 * Class CreateDatabase
 */
class CreateDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable();
            
            $table->timestamps();
        });
        
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->nullable();
            
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
        
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            
            $table->integer('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('orders')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
        
        Schema::create('sellers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            
            $table->timestamps();
        });
        
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            
            $table->integer('seller_id')->unsigned()->nullable()->index();
            $table->foreign('seller_id')->references('id')->on('sellers')
                ->onUpdate('cascade')->onDelete('cascade');
                
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            
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
        Schema::drop('order_items');
        Schema::drop('users');
        Schema::drop('orders');
    }
}