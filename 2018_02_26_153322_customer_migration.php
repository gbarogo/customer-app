<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->string('dni');
            $table->string('phone_number');
            $table->string('name');
            $table->string('second_surname');
            $table->string('surname');

            // Añadimos la clave foránea con Userdb. userdb_email
            // Acordarse de añadir al array protected $fillable del fichero de modelo "Customer.php" la nueva columna:
            //$table->integer('user_email')->unsigned();

            // Indicamos cual es la clave foránea de esta tabla:
            //$table->foreign('user_email')->references('email')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer');
    }
}
