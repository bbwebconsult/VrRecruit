<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check history log of tasks is working');
$task = $I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34777777777', 'state'=> Task::STATE_PENDING]);
$I->seeInDatabase('tasks_history', ['id_task' => $task->id, 'action_taker' => 'Property Manager', 'state' => Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34777777777', 'Body' => 'YES']);
$I->seeInDatabase('tasks_history', ['id_task' => $task->id, 'action_taker' => 'Benoit', 'state' => Task::STATE_ACCEPTED]);