<?php

use Vreasy\Models\Task;

class UpdateTasksTable extends Ruckusing_Migration_Base
{
     public function up()
    {
    	$this->add_column('tasks', 'state', 'integer', ['default' => Task::STATE_PENDING]); //add state column. Use Pending state by default 
    }//up()

    public function down()
    {
        $this->remove_column("tasks", 'state');
    }//down
}
