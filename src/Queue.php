<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 06.05.18
 * Time: 22:57
 */

namespace alexeysteklov\yii\queue\pgq;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\di\Instance;
use yii\queue\cli\Queue as CliQueue;

class Queue extends CliQueue
{
    /**
     * @var array|ConnectorInterface|string
     */
    public $connector = [
        'class' => Connector::class,
        'db' => 'db',
    ];
    /** @var string Имя очереди */
    public $queue;

    /** @var string Тип сообщений */
    public $type = 'yii2-queue-pgq';

    /** @var string Имя потребителя */
    public $consumer = 'consumer';

    /** @var int Время через которое нужно повторить ошибочное задание */
    public $retryTimeout = 30;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->connector = Instance::ensure($this->connector,
            ConnectorInterface::class);
        if (empty($this->queue)) {
            throw new InvalidConfigException('Пропущен обязательный параметр queue');
        }
        if (empty($this->type)) {
            throw new InvalidConfigException('Пропущен обязательный параметр type');
        }
    }

    /**
     * Listens queue and runs each job.
     *
     * @param bool $repeat whether to continue listening when queue is empty.
     * @param int $timeout number of seconds to sleep before next iteration.
     * @return null|int exit code.
     * @internal for worker command only
     * @since 2.0.2
     */
    public function run($repeat, $timeout = 0)
    {
        return $this->runWorker(function (callable $canContinue) use (
            $repeat,
            $timeout
        ) {
            while ($canContinue()) {
                if ($batchId = $this->connector->getNextBatchId($this->queue,
                    $this->consumer)) {
                    $events = $this->connector->getBatchEvents($batchId);
                    $this->connector->db->beginTransaction();
                    foreach ($events as $event) {
                        if (!$this->handleMessage(
                            $event->id,
                            $event->data,
                            $event->extra1,
                            $event->retry
                        )) {
                            $this->connector->eventRetry($batchId, $event->id,
                                $this->retryTimeout);
                        }
                    }
                    $this->connector->finishBatch($batchId);
                    $this->connector->db->getTransaction()->commit();
                } elseif (!$repeat) {
                    break;
                } elseif ($timeout) {
                    sleep($timeout);
                }
            }
        });
    }

    /**
     * @param string $message
     * @param int $ttr time to reserve in seconds
     * @param int $delay
     * @param mixed $priority
     * @return string id of a job message
     * @throws \yii\base\NotSupportedException
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        if ($delay) {
            throw new NotSupportedException('Delayed work is not supported in the driver.');
        }
        if ($priority !== null) {
            throw new NotSupportedException('Job priority is not supported in the driver.');
        }

        return $this->connector->insertEvent($this->queue, $this->type,
            $message, $ttr);
    }

    /**
     * @param string $id of a job message
     * @throws \yii\base\NotSupportedException
     */
    public function status($id)
    {
        throw new NotSupportedException('Status is not supported in the driver.');
    }
}