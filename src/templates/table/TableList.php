<?php


namespace Yirius\Admin\templates\table;

use Yirius\Admin\templates\table\columns\ColumnsList;
use Yirius\Admin\templates\table\event\EventList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class TableList
 * @package Yirius\Admin\templates\table
 * @method TableJs Tablejs();
 * @method SortJs Sortjs();
 */
class TableList
{
    private $classList = [
        "tablejs" => TableJs::class,
        "sortjs" => SortJs::class
    ];

    private $event = null;

    /**
     * @title      event
     * @description
     * @createtime 2020/5/26 9:45 下午
     * @return EventList
     * @author     yangyuance
     */
    public function event() {
        if(is_null($this->event)) {
            $this->event = new EventList();
        }
        return $this->event;
    }

    private $columns = null;

    /**
     * @title      columns
     * @description
     * @createtime 2020/5/27 5:55 下午
     * @return ColumnsList|null
     * @author     yangyuance
     */
    public function columns() {
        if(is_null($this->columns)) {
            $this->columns = new ColumnsList();
        }
        return $this->columns;
    }


    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if(isset($this->classList[$name])) {
            return (new $this->classList[$name]());
        } else {
            ThinkerAdmin::response()->msg("未找到对应TableList:" . $name)->fail();
        }
    }
}