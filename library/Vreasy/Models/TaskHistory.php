<?php

namespace Vreasy\Models;

use Vreasy\Query\Builder;

class TaskHistory extends Base
{
    // Protected attributes should match table columns
    protected $id_task;
    protected $action_taker;
    protected $state;
    protected $time;

    public function __construct()
    {
        // Validation is done run by Valitron library
        $this->validates(
            'required',
            ['id_task', 'action_taker', 'state', 'time']
        );
        $this->validates(
            'date',
            ['time']
        );
        $this->validates(
            'integer',
            ['id_task', 'state']
        );
    }

    public function save()
    {
        // Base class forward all static:: method calls directly to Zend_Db
        if ($this->isValid()) {
            $this->time = gmdate(DATE_FORMAT);
            if ($this->isNew()) {
                static::insert('tasks_history', $this->attributesForDb());
                $this->id = static::lastInsertId();
            }
            //no update. History log hsould never be updated
            return $this->id;
        }
    }

    public static function where($params, $opts = [])
    {
        // Default options' values
        $limit = 0;
        $start = 0;
        $orderBy = ['time'];
        $orderDirection = ['asc'];
        extract($opts, EXTR_IF_EXISTS);
        $orderBy = array_flatten([$orderBy]);
        $orderDirection = array_flatten([$orderDirection]);

        // Return value
        $collection = [];
        // Build the query
        list($where, $values) = Builder::expandWhere(
            $params,
            ['wildcard' => true, 'prefix' => 't.']);

        // Select header
        $select = "SELECT t.* FROM tasks_history AS t";

        // Build order by
        foreach ($orderBy as $i => $value) {
            $dir = isset($orderDirection[$i]) ? $orderDirection[$i] : 'ASC';
            $orderBy[$i] = "`$value` $dir";
        }
        $orderBy = implode(', ', $orderBy);

        $limitClause = '';
        if ($limit) {
            $limitClause = "LIMIT $start, $limit";
        }

        $orderByClause = '';
        if ($orderBy) {
            $orderByClause = "ORDER BY $orderBy";
        }
        if ($where) {
            $where = "WHERE $where";
        }

        $sql = "$select $where $orderByClause $limitClause";
        if ($res = static::fetchAll($sql, $values)) {
            foreach ($res as $row) {
                $collection[] = static::instanceWith($row);
            }
        }
        return $collection;
    }
}
