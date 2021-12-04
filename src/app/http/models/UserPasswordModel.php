<?php

namespace ksoftm\app\http\models;

use ksoftm\system\model\BaseModel;
use ksoftm\system\utils\validator\MegRule;

class UserPasswordModel extends BaseModel
{
    protected array $primaryKeys = [
        // "id"
    ];

    protected array $uniqueKeys = [];

    public function rules(): array
    {
        return [
            // MegRule::new('id')->int()->unsigned()
        ];
    }
}
