<?php

namespace app\models;

use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'post';
    }

    // Свойства: id, title, content, created_at
    // Можно добавить rules() для валидации на уровне БД/форм
}
