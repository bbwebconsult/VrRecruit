<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check that bad answers are ignored');
$I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34555555555', 'state'=> Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34555555555', 'Body' => 'I\ll do it!']);
$I->seeInDatabase('tasks', ['assigned_phone' => '+34555555555', 'state' => Task::STATE_PENDING]);