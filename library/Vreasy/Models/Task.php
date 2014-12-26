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
    protected $state;

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
            ['id', 'states']
        );
    }

    public function save()
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

    /*
     * Return the corresponding string (name) of each state
     */
    public function getStateString()
    {
    	switch($this->state)
    	{
    		case self::STATE_PENDING:
    			return 'Pending';
    		break;
    		case self::STATE_COMPLETED:
    			return 'Completed';
    		break;
    		case self::STATE_ACCEPTED:
    			return 'Accepted';
    		break;
    		case self::STATE_REFUSED:
    			return 'Refused';
    		break;
    		default:
    			return 'Errr...';
    		break;
    	}
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
    
    
    public function attributes($options = [])
    {
    	$attributes = parent::attributes($options);
    	$attributes['state_string'] = $this->getStateString();
    	
    	return $attributes;
    }
    
}
