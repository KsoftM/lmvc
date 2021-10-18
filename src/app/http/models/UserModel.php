<?php

namespace ksoftm\app\http\models;

use ksoftm\system\model\BaseModel;
use ksoftm\system\utils\validator\MegRule;

class UserModel extends BaseModel
{
    protected array $primaryKeys = [
        "id"
    ];

    protected array $uniqueKeys = [
        'username', 'email'
    ];

    protected array $hidden = [
        'password', "created_time", "updated_time"
    ];

    public function rules(): array
    {
        return [
            MegRule::new('firstName')->required()->string()->max(50),
            MegRule::new('lastName')->required()->string()->max(50),
            MegRule::new('username')->required()->string()->nullable()->max(50)->userName()
            // ->unique($this->tableName())
            ,
            MegRule::new('email')->required()->string()->max(300)->email()
            // ->unique($this->tableName())
            ,
            MegRule::new('password')->required()->string()->max(500)->password(),
            MegRule::new('batch')->required()->int()->unsigned(),
            MegRule::new('active')->required()->int()->unsigned()->set([0, 1]),
            MegRule::new('roles_id')->required()->int()->unsigned(),
        ];
    }
}
