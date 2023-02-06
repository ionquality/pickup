<?php

use App\Models\Security;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('op_id')->nullable()->default(null);
            $table->string('first_name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('active','2')->default('Y');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $operators = Security::all();

        foreach ($operators as $operator){
            User::create([
                'op_id' => $operator->op_id,
                'first_name' => trim($operator->first_name),
                'last_name' => trim($operator->last_name),
                'username' => $operator->op_passnum,
                'password' => Hash::make($operator->op_passnum),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
