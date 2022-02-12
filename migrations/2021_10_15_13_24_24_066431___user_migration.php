<?php

use ksoftm\system\database\RawQuery;
use ksoftm\system\Schema;
use ksoftm\system\utils\database\Migration;

class UserMigration extends Migration
{

    public function up(): void
    {
        Schema::CreateIfNotExists('users', function (RawQuery $query) {
            $query->id();
            $query->string('firstName', 50);
            $query->string('lastName', 50);
            $query->string('username', 50)->unique();
            $query->string('email', 300)->unique();
            $query->longText('password');
            $query->boolean('active')->nullable()->default('1');

            $query->timestamps();
        });
    }

    public function down(): void
    {
        Schema::DropIfExists('users');
    }
}
