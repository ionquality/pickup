<?php

use App\Models\Security;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        $operators = Security::all();

        foreach ($operators as $operator){
            $exist = User::where('op_id', $operator->op_id)->exists();
            if (!$exist){
                $name = trim($operator->first_name) . ' ' . trim($operator->last_name);
                User::create([
                    'op_id' => $operator->op_id,
                    'first_name' => trim($operator->first_name),
                    'last_name' => trim($operator->last_name),
                    'name' => $name,
                    'username' => $operator->op_passnum,
                    'password' => Hash::make($operator->op_passnum),
                ]);
            }

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
