<?php

use ksoftm\system\database\RawQuery;
use ksoftm\system\Schema;
use ksoftm\system\utils\database\Migration;

class LessonsMigration extends Migration
{

    public function up(): void
    {
        Schema::CreateIfNotExists('lessons', function (RawQuery $query) {
            $query->id();
            $query->boolean('active')->default(1);

            $query->timestamps();
        });
    }

    public function down(): void
    {
        Schema::DropIfExists('lessons');
    }
}
