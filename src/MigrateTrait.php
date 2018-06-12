<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 13.11.16
 * Time: 14:51
 */

namespace alexeysteklov\yii\queue\pgq;

trait MigrateTrait
{
    /**
     * Создание новой очереди
     * @param $name string Имя новой очереди
     */
    public function createQueue($name)
    {
        $this->execute('SELECT pgq.create_queue(:queue_name)',
          [':queue_name' => $name]);
    }

    /**
     * Executes a SQL statement.
     * This method executes the specified SQL statement using [[db]].
     * @param string $sql the SQL statement to be executed
     * @param array $params input parameters (name => value) for the SQL execution.
     * See [[Command::execute()]] for more details.
     */
    public abstract function execute($sql, $params = []);

    /**
     * Удаление очереди
     * @param $name string Имя очереди
     */
    public function dropQueue($name)
    {
        $this->execute('SELECT pgq.drop_queue(:queue_name)',
          [':queue_name' => $name]);
    }

    /**
     * Регистрация потребителя
     * @param $queue string Имя очереди
     * @param $consumer string Имя потребителя
     */
    public function registerConsumer($queue, $consumer)
    {
        \Yii::$app->db->masterPdo->
        $this->execute('SELECT pgq.register_consumer(:queue_name, :consumer_name)',
          [':queue_name' => $queue, ':consumer_name' => $consumer]);
    }

    /**
     * Удаление регистрации потребителя
     * @param $queue string Имя очереди
     * @param $consumer string Имя потребителя
     */
    public function unRegisterConsumer($queue, $consumer)
    {
        $this->execute('SELECT pgq.unregister_consumer(:queue_name, :consumer_name)',
          [':queue_name' => $queue, ':consumer_name' => $consumer]);
    }


}