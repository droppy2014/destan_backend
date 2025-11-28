<?php

namespace app\services;

use app\forms\PostCreateForm;
use app\models\Post;
use app\repositories\PostRepositoryInterface;

/**
 * Сервис доменной логики постов.
 *
 * Обрабатывает создание постов и выборку ленты.
 * Инкапсулирует бизнес-логику, чтобы:
 *  - контроллер оставался тонким;
 *  - репозиторий занимался только БД;
 *  - формы отвечали за валидацию входных данных.
 */
class PostService
{
    /**
     * Репозиторий постов.
     * Работает через интерфейс, чтобы можно было подменять реализацию
     * (например, на другую БД или кеш).
     *
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;

    /**
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Возвращает ленту постов в выбранном режиме.
     *
     * Режимы:
     * - random        — случайный порядок
     * - last_minute   — посты за последнюю минуту
     * - chronological — обычная хронология (по умолчанию)
     *
     * @param string $mode
     * @return Post[]
     */
    public function getFeed(string $mode): array
    {
        switch ($mode) {
            case 'random':
                return $this->postRepository->getAllRandom();

            case 'last_minute':
                return $this->postRepository->getLastMinute();

            case 'chronological':
                return $this->postRepository->getChronological();

            default:
                // если режим неизвестный — возвращаем дефолт
                return $this->postRepository->getChronological();
        }
    }

    /**
     * Создаёт новый пост на основе валидированной формы.
     *
     * - формирует модель Post
     * - выставляет дату создания
     * - сохраняет через репозиторий
     *
     * Валидация выполняется заранее в PostCreateForm.
     *
     * @param PostCreateForm $form
     * @return Post
     */
    public function create(PostCreateForm $form): Post
    {
        $post = new Post();
        $post->title = $form->title;
        $post->content = $form->content;
        $post->created_at = date('Y-m-d H:i:s');

        $this->postRepository->save($post);

        return $post;
    }
}
