<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 13.11.16
 * Time: 16:28
 */

namespace alexeysteklov\yii\queue\pgq;

/**
 * Interface ConnectorInterface
 * @package alexeysteklov\yii\queue\pgq
 * @property \yii\db\Connection db Подключение к БД
 */
interface ConnectorInterface
{
    /**
     * Получить новую пачку заданий.
     * @param $queue string Имя очереди
     * @param $consumer string Имя потребителя
     * @return int Идентификатор пачки
     * @throws \yii\db\Exception
     */
    public function getNextBatchId($queue, $consumer);

    /**
     * Получить список
     * @param $batchId int Идентификатор пачки
     * @return Event[] Массив ивентов
     */
    public function getBatchEvents($batchId);

    /**
     * Повторить событие через несколько секунд
     * @param $batchId int Идентификатор пачки
     * @param $eventId int Идентификатор события
     * @param $timeout int Колличество секунд через которое нужно повторить событие
     */
    public function eventRetry($batchId, $eventId, $timeout);

    /**
     * Завершение пачки событий
     * @param $batchId int Идентификатор пачки
     */
    public function finishBatch($batchId);

    /**
     * Вставка нового события
     * @param $queue string Имя очереди
     * @param $type string Тип события
     * @param $data string Данные события
     * @param string $extra_1
     * @param string $extra_2
     * @param string $extra_3
     * @param string $extra_4
     * @return int Идентификатор события
     */
    public function insertEvent(
        $queue,
        $type,
        $data,
        $extra_1 = null,
        $extra_2 = null,
        $extra_3 = null,
        $extra_4 = null
    );
}