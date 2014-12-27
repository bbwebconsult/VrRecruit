<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check acceptance of a task');
$I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34111111111', 'state'=> Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34111111111', 'Body' => 'YES']);
$I->seeInDatabase('tasks', ['assigned_phone' => '+34111111111', 'state' => Task::STATE_ACCEPTED]);