<?php

use Vreasy\Models\Task;

$I = new TestGuy($scenario);
$I->wantTo('Check that unknown incoming phone numbers return error message');
$I->haveTask(['assigned_name' => 'Benoit', 'assigned_phone' => '+34666666666', 'state'=> Task::STATE_PENDING]);
$I->sendPOST('/twilio/update?format=json', ['From' => '+34555555555', 'Body' => 'I\ll do it!']);
$I->see('NO task was found assigned to the incoming number');