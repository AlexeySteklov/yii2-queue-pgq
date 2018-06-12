<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 12.06.18
 * Time: 15:55
 */

namespace alexeysteklov\yii\queue\pgq;

use yii\console\Exception;

class Command extends \yii\queue\cli\Command
{


    public $isolate = false;

    /**
     * @var Queue
     */
    public $queue;

    /**
     * @param string $actionID
     * @return bool
     * @since 2.0.2
     */
    protected function isWorkerAction($actionID)
    {
        return in_array($actionID, ['run', 'listen'], true);
    }

    /**
     * Runs all jobs from db-queue.
     * It can be used as cron job.
     *
     * @return null|int exit code.
     */
    public function actionRun()
    {
        return $this->queue->run(false);
    }

    /**
     * Listens db-queue and runs new jobs.
     * It can be used as daemon process.
     *
     * @param int $timeout number of seconds to sleep before next reading of the queue.
     * @throws Exception when params are invalid.
     * @return null|int exit code.
     */
    public function actionListen($timeout = 3)
    {
        if (!is_numeric($timeout)) {
            throw new Exception('Timeout must be numeric.');
        }
        if ($timeout < 1) {
            throw new Exception('Timeout must be greater that zero.');
        }
        return $this->queue->run(true, $timeout);
    }

}