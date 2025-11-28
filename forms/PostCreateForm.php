<?php

namespace app\forms;

use yii\base\Model;

/**
 * Class PostCreateForm
 *
 * Форма для создания поста через API.
 * Используется как DTO + слой валидации.
 *
 * Поля заполняются из JSON-запроса в контроллере.
 * После успешной валидации данные передаются в PostService.
 */
class PostCreateForm extends Model
{
    /**
     * Заголовок поста.
     * Пользователь должен передать строку, максимум 255 символов.
     *
     * @var string
     */
    public string $title = '';

    /**
     * Основной текст поста.
     * Может содержать любую строку, ограничений по длине нет.
     *
     * @var string
     */
    public string $content = '';

    /**
     * Правила валидации формы.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // оба поля обязательны
            [['title', 'content'], 'required'],

            // title — строка до 255 символов
            ['title', 'string', 'max' => 255],

            // content — просто строка (любой длины)
            ['content', 'string'],
        ];
    }
}
