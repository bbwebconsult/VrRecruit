<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check refusal of a task');
$I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34444444444', 'state'=> Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34444444444', 'Body' => 'NO']);
$I->seeInDatabase('tasks', ['assigned_phone' => '+34444444444', 'state' => Task::STATE_REFUSED]);