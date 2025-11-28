<?php

namespace app\repositories;

use app\models\Post;

/**
 * Интерфейс репозитория постов.
 *
 * Определяет контракт для всех реализаций,
 * чтобы сервисы могли работать с PostRepository,
 * не зная деталей реализации (БД, кеш, API и т.п.).
 */
interface PostRepositoryInterface
{
    /**
     * Сохраняет модель Post.
     * Реализация должна обрабатывать ошибку сохранения
     * (логирование, исключение или иное поведение).
     *
     * @param Post $post
     */
    public function save(Post $post): void;

    /**
     * Возвращает все посты в случайном порядке.
     *
     * @return Post[]
     */
    public function getAllRandom(): array;

    /**
     * Возвращает посты, созданные за последнюю минуту.
     *
     * @return Post[]
     */
    public function getLastMinute(): array;

    /**
     * Возвращает хронологическую ленту:
     * от самых старых к более новым.
     *
     * @return Post[]
     */
    public function getChronological(): array;
}
