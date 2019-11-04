<?php

namespace htmlacademy\taskforce;

class Task
{
    protected $id_client; // id заказчика.
    protected $id_performer; // id исполнителя.
    protected $finish_date; // срок завершения.
    protected $status; // текущий статус.

    // Возвращает список статусов. Всех? только названия? Список констант?
    public function getStatusList() {

    }

    // Возвращает список действий. Всех? только названия? Список констант?
    public function getActionList() {

    }
}

