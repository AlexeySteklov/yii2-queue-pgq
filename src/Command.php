<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 12.06.18
 * Time: 15:55
 */

namespace alexeysteklov\yii\queue\pgq;


class Command extends \yii\queue\cli\Command
{


    public $isolate = false;

    /**
     * @param string $actionID
     * @return bool
     * @since 2.0.2
     */
    protected function isWorkerAction($actionID)
    {
        return in_array($actionID, ['run', 'listen'], true);
    }
}