<?php

use ksoftm\system\database\RawQuery;
use ksoftm\system\Schema;
use ksoftm\system\utils\database\Migration;

class UserMigration extends Migration
{

    public function up(): void
    {
        Schema::CreateIfNotExists('test', function (RawQuery $query) {
            $query->id();
            $query->string('firstName', 50);
            $query->string('lastName', 50);
            $query->string('username', 50)->nullable()->unique();
            $query->string('email', 300)->unique();
            $query->string('password', 500);
            $query->year('batch');
            $query->boolean('active')->default(0);
            $query->integer('roles_id')->index();

            $query->timestamps();
        });
    }

    public function down(): void
    {
        Schema::DropIfExists('test');
    }
}
