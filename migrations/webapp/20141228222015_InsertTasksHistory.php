<?php

use Vreasy\Models\Task;

class InsertTasksHistory extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->execute("INSERT INTO tasks_history (id_task, state, action_taker, time) VALUES(1 , ".Task::STATE_PENDING.",'Property Manager', '".(new \DateTime("-1 days"))->format(DATE_FORMAT)."')");
        $this->execute("INSERT INTO tasks_history (id_task, state, action_taker, time) VALUES(1 , ".Task::STATE_ACCEPTED.",'John Doe', '".(new \DateTime())->format(DATE_FORMAT)."')");
    }//up()

    public function down()
    {
    }//down()
}
