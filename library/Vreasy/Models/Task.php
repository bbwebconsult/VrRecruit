<?php

namespace Vreasy\Models;

use Vreasy\Query\Builder;

class Task extends Base
{
    // Protected attributes should match table columns
    protected $id;
    protected $deadline;
    protected $assigned_name;
    protected $assigned_phone;
    protected $created_at;
    protected $updated_at;
    protected $state = self::STATE_PENDING;

    const STATE_PENDING = 1;
    const STATE_REFUSED = 2;
    const STATE_ACCEPTED = 3;
    const STATE_COMPLETED = 4;
    
    public function __construct()
    {
        // Validation is done run by Valitron library
        $this->validates(
            'required',
            ['deadline', 'assigned_name', 'assigned_phone', 'state']
        );
        $this->validates(
            'date',
            ['created_at', 'updated_at']
        );
        $this->validates(
            'integer',
            ['id', 'state']
        );
    }

    public function save($params = [])
    {
        // Base class forward all static:: method calls directly to Zend_Db
        if ($this->isValid()) {
            $this->updated_at = gmdate(DATE_FORMAT);
            if ($this->isNew()) {
                $this->created_at = $this->updated_at;
                static::insert('tasks', $this->attributesForDb());
                $this->id = static::lastInsertId();
            } else {
                static::update(
                    'tasks',
                    $this->attributesForDb(),
                    ['id = ?' => $this->id]
                );
            }
            
            $log = TaskHistory::instanceWith([
                'id_task' => $this->id,
                'state' => $this->state,
                'time' => gmdate(DATE_FORMAT),
                'action_taker' => isset($params['action_taker']) ? $params['action_taker'] : 'Property Manager', 
            ]);
            
            if($log->isValid())
                $log->save();
            
            
            return $this->id;
        }
    }

    public static function findOrInit($id)
    {
        $task = new Task();
        if ($tasksFound = static::where(['id' => (int)$id])) {
            $task = array_pop($tasksFound);
        }
        return $task;
    }

    public static function where($params, $opts = [])
    {
        // Default options' values
        $limit = 0;
        $start = 0;
        $orderBy = ['created_at'];
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
        $select = "SELECT t.* FROM tasks AS t";

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
