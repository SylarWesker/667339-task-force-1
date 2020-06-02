<?php


namespace frontend\models\query;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see Task
 */
class TaskQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Получает информацию для представления с заданиями.
     * @return TaskQuery
     */
    public function forTasksPageView()
    {
        return parent::select(['description', 'details', 'address', 'budget', 'creation_date', 'category_id'])
            ->with('category')
            ->withStatusNew()
            ->firstNewCreated();
    }

    /**
     * Сортирует по дате создания (сначала новые).
     *
     * {@inheritdoc}
     * @return TaskQuery
     */
    public function firstNewCreated($db = null)
    {
        return parent::orderBy(['creation_date' => SORT_DESC]);
    }

    /**
     * Выбирает только задания со статусом "Новое".
     *
     * {@inheritdoc}
     * @return TaskQuery
     */
    public function withStatusNew($db = null)
    {
        return parent::where(['status_id' => 1]);
    }
}
