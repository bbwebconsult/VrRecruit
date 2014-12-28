<?php

$cliIndex = implode(DIRECTORY_SEPARATOR, ['Vreasy', 'application', 'cli', 'cliindex.php']);
require_once($cliIndex);

use Vreasy\Models\Task;

class InsertSomeTasks extends Ruckusing_Migration_Base
{
    public function up()
    {
        
        $date = gmdate(DATE_FORMAT);
        foreach ([1,2,3] as $i) {
            $this->execute("INSERT INTO tasks (deadline, assigned_name, assigned_phone, created_at, updated_at) VALUES('".(new \DateTime("+$i days"))->format(DATE_FORMAT)."', 'John Doe', '+55 555-555-555', '$date', '$date');");
        }
    }//up()

    public function down()
    {
    }//down()
}
