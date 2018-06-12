<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 13.11.16
 * Time: 17:08
 */

namespace alexeysteklov\yii\queue\pgq;


use yii\base\BaseObject;
use yii\db\Connection;
use yii\di\Instance;

class Connector extends BaseObject implements ConnectorInterface
{
    /** @var Connection|array|string Имя компонента БД */
    public $db = 'db';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * Получить новую пачку заданий.
     * @param $queue string Имя очереди
     * @param $consumer string Имя потребителя
     * @return int Идентификатор пачки
     * @throws \yii\db\Exception
     */
    public function getNextBatchId($queue, $consumer)
    {
        return $this->db->createCommand('SELECT pgq.next_batch(:queue,:consumer)',
            [':queue' => $queue, ':consumer' => $consumer])->queryScalar();
    }

    /**
     * Получить список
     * @param $batchId int Идентификатор пачки
     * @return Event[] Массив ивентов
     * @throws \yii\db\Exception
     */
    public function getBatchEvents($batchId)
    {
        $eventsRaw = $this->db->createCommand('SELECT pgq.get_batch_events(:batchId)',
            [':batchId' => $batchId])->queryAll();
        $result = [];
        foreach ($eventsRaw as $eventRaw) {
            $item = new Event();
            $item->id = $eventRaw['ev_id'];
            $item->time = (new \DateTime())->setTimestamp(strtotime($eventRaw['ev_time']));
            $item->txid = $eventRaw['ev_txid'];
            $item->retry = $eventRaw['ev_retry'];
            $item->type = $eventRaw['ev_type'];
            $item->data = $eventRaw['ev_data'];
            $item->extra1 = $eventRaw['ev_extra1'];
            $item->extra2 = $eventRaw['ev_extra2'];
            $item->extra3 = $eventRaw['ev_extra3'];
            $item->extra4 = $eventRaw['ev_extra4'];
            $result[] = $item;
        }
        return $result;
    }

    /**
     * Повторить событие через несколько секунд
     * @param $batchId int Идентификатор пачки
     * @param $eventId int Идентификатор события
     * @param $timeout int Колличество секунд через которое нужно повторить событие
     * @throws \yii\db\Exception
     */
    public function eventRetry($batchId, $eventId, $timeout)
    {
        $this->db->createCommand('SELECT pgq.event_retry(:batchId,:eventId,:retrySeconds)',
            [
                ':batchId' => $batchId,
                ':eventId' => $eventId,
                ':retrySeconds' => $timeout
            ])->execute();
    }

    /**
     * Завершение пачки событий
     * @param $batchId int Идентификатор пачки
     * @throws \yii\db\Exception
     */
    public function finishBatch($batchId)
    {
        $this->db->createCommand('SELECT pgq.finish_batch(:batchId)',
            [':batchId' => $batchId])->execute();
    }

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
     * @throws \yii\db\Exception
     */
    public function insertEvent(
        $queue,
        $type,
        $data,
        $extra_1 = null,
        $extra_2 = null,
        $extra_3 = null,
        $extra_4 = null
    ) {
        return $this->db->createCommand('SELECT pgq.insert_event(:queue,:type. :data, :extra_1, :extra_2, :extra_3, :extra_4)',
            [
                ':queue' => $queue,
                ':type' => $type,
                ':data' => $data,
                ':extra_1' => $extra_1,
                ':extra_2' => $extra_2,
                ':extra_3' => $extra_3,
                ':extra_4' => $extra_4
            ])->queryScalar();
    }
}