<?php
/**
 * Created by PhpStorm.
 * User: Alexey Steklov
 * Date: 13.11.16
 * Time: 16:52
 */

namespace alexeysteklov\yii\queue\pgq;


/**
 * Событие PGQ
 * Class Event
 * @package app\components\pgq
 */
class Event
{
    /** @var int Идентификатор события */
    public $id;
    /** @var \DateTime Время события */
    public $time;
    /** @var int  txid */
    public $txid;
    /** @var int  Колличество попыток повтора события */
    public $retry;
    /** @var string Тип заданый при создании */
    public $type;
    /** @var string Данные события */
    public $data;
    /** @var string Экстра данные события 1*/
    public $extra1;
    /** @var string Экстра данные события 2*/
    public $extra2;
    /** @var string Экстра данные события 3*/
    public $extra3;
    /** @var string Экстра данные события 4*/
    public $extra4;
}