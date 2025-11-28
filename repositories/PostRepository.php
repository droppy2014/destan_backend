<?php

namespace app\repositories;

use app\models\Post;
use yii\db\Expression;

/**
 * Репозиторий для работы с данными постов.
 *
 * Отвечает за чтение/запись Post из БД.
 * Инкапсулирует SQL-логику и делает сервисы чище.
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * Сохраняет модель Post.
     * Если сохранение не удалось — можно добавить логирование или исключение.
     *
     * @param Post $post
     */
    public function save(Post $post): void
    {
        if (!$post->save()) {
            // тут можно бросить исключение или залогировать ошибку,
            // но оставлено пустым по текущей логике
        }
    }

    /**
     * Возвращает посты в случайном порядке.
     * Используется RAND(), подходит для небольших выборок.
     *
     * @return Post[]
     */
    public function getAllRandom(): array
    {
        return Post::find()
            ->orderBy(new Expression('RAND()'))
            ->all();
    }

    /**
     * Посты за последнюю минуту.
     * Выбирает записи, у которых created_at >= NOW() - 60 секунд.
     *
     * @return Post[]
     */
    public function getLastMinute(): array
    {
        $time = time() - 60;

        return Post::find()
            ->where(['>=', 'created_at', $time])
            ->orderBy(['created_at' => SORT_DESC]) // новые первыми
            ->all();
    }

    /**
     * Хронологическая лента постов.
     * Сортировка от старых к новым (возрастающий порядок).
     *
     * @return Post[]
     */
    public function getChronological(): array
    {
        return Post::find()
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }
}
