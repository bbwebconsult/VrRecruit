<?php

use Vreasy\Models\Task;

class CreateTasksHistoryTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $tasks = $this->create_table('tasks_history', ['id' => false, 'options' => 'Engine=InnoDB']);
        $tasks->column('id_task','integer');
        $tasks->column('action_taker','text');
        $tasks->column('state','integer');
        $tasks->column('time','datetime');
        $tasks->finish();
        
        $this->execute("ALTER TABLE tasks_history ADD CONSTRAINT fk_task FOREIGN KEY(id_task)"
            ." REFERENCES tasks(id) ON DELETE CASCADE ON UPDATE CASCADE");
    }//up()

    public function down()
    {
        $this->drop_table("tasks_history");
    }//down()
}
