<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check acceptance of a task with typo');
$I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34222222222', 'state'=> Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34222222222', 'Body' => 'YEES']);
$I->seeInDatabase('tasks', ['assigned_phone' => '+34222222222', 'state' => Task::STATE_ACCEPTED]);