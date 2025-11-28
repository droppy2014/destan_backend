<?php

declare(strict_types=1);

namespace app\controllers\api;

use app\forms\PostCreateForm;
use app\services\PostService;
use Yii;
use yii\base\Action;
use yii\filters\Cors;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class PostController
 *
 * REST-контроллер для работы с постами.
 * Отвечает за:
 *  - создание поста (POST /api/posts);
 *  - получение ленты постов в разных режимах (GET /api/posts).
 */
class PostController extends Controller
{
    /**
     * Сервис доменной логики для работы с постами.
     *
     * @var PostService
     */
    private PostService $postService;

    /**
     * Отключаем CSRF-валидацию, так как контроллер работает как JSON-API.
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * PostController constructor.
     *
     * В конструктор внедряется PostService (Dependency Injection),
     * чтобы контроллер не создавал сервис самостоятельно и оставался тонким.
     *
     * @param string      $id        Идентификатор контроллера в рамках модуля.
     * @param \yii\base\Module $module    Модуль, к которому относится контроллер.
     * @param PostService $postService Сервис для работы с постами.
     * @param array       $config    Дополнительная конфигурация контроллера.
     */
    public function __construct($id, $module, PostService $postService, $config = [])
    {
        $this->postService = $postService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Подключает поведения контроллера.
     *
     * Здесь настраивается CORS, чтобы к API можно было обращаться с фронтенда
     * (например, SPA на http://localhost:5173).
     *
     * @return array<string, mixed>
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors'  => [
                // С каких доменов разрешены запросы
                'Origin'                           => ['http://localhost:5173'],
                // Разрешённые HTTP-методы
                'Access-Control-Request-Method'    => ['GET', 'POST', 'OPTIONS'],
                // Разрешённые заголовки
                'Access-Control-Request-Headers'   => ['*'],
                // Разрешена ли передача cookie/авторизационных заголовков
                'Access-Control-Allow-Credentials' => true,
                // Время кеширования preflight-ответа (OPTIONS) в секундах
                'Access-Control-Max-Age'           => 86400,
            ],
        ];

        return $behaviors;
    }

    /**
     * Общая предобработка каждого экшена.
     *
     * Здесь:
     *  - обрабатываем preflight-запросы (OPTIONS) для CORS;
     *  - принудительно выставляем формат ответа в JSON.
     *
     * @param Action $action Текущий вызываемый экшен.
     *
     * @return bool Возвращает false, если выполнение экшена нужно прервать.
     */
    public function beforeAction($action): bool
    {
        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        // Обработка preflight-запроса CORS (OPTIONS)
        if ($request->method === 'OPTIONS') {
            $response->statusCode = 204; // No Content
            return false; // Экшен дальше не выполняем
        }

        // Все ответы из этого контроллера отдаем в JSON
        $response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * Создание нового поста.
     *
     * Маршрут: POST /api/posts
     *
     * Ожидает JSON-тело запроса с полями, описанными в PostCreateForm.
     * Пример тела:
     * {
     *   "title": "Заголовок",
     *   "content": "Текст поста"
     * }
     *
     * В случае валидационной ошибки возвращает HTTP-статус 422 и массив ошибок.
     *
     * @return array<string, mixed> Структура ответа API:
     *                              - success: bool
     *                              - errors?: array<string, string[]>
     *                              - data?: array<string, mixed>
     */
    public function actionCreate(): array
    {
        $form = new PostCreateForm();
        // Загружаем данные из JSON-тела запроса, без префикса формы
        $form->load(Yii::$app->request->bodyParams, '');

        if (!$form->validate()) {
            Yii::$app->response->statusCode = 422;

            return [
                'success' => false,
                'errors'  => $form->getErrors(),
            ];
        }

        // Выносим создание поста в доменный сервис
        $post = $this->postService->create($form);

        return [
            'success' => true,
            'data'    => [
                'id'         => $post->id,
                'title'      => $post->title,
                'content'    => $post->content,
                'created_at' => $post->created_at,
            ],
        ];
    }

    /**
     * Получение ленты постов.
     *
     * Маршрут: GET /api/posts?mode=random|last_minute|chronological
     *
     * Параметр $mode определяет стратегию выборки:
     *  - "chronological" (по умолчанию) — обычная хронология;
     *  - "random"        — случайный порядок;
     *  - "last_minute"   — посты за последнюю минуту (пример).
     *
     * @param string $mode Режим выдачи ленты постов.
     *
     * @return array<string, mixed> Структура ответа API:
     *                              - success: bool
     *                              - data: array<int, array<string, mixed>>
     */
    public function actionIndex(string $mode = 'chronological'): array
    {
        $posts = $this->postService->getFeed($mode);

        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id'         => $post->id,
                'title'      => $post->title,
                'content'    => $post->content,
                'created_at' => $post->created_at,
            ];
        }

        return [
            'success' => true,
            'data'    => $data,
        ];
    }
}
