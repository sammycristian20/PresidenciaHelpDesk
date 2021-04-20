<?php

use App\Plugins\Calendar\Model\TaskTemplate;
use App\Plugins\Calendar\Model\TemplateTask;
use Faker\Generator as Faker;

$factory->define(
    TemplateTask::class, function (Faker $faker) {
    return [
        'template_id' => factory(TaskTemplate::class)->create()->id,
        'name' => $faker->name,
        'end' => 100,
        'end_unit' => 'minute',
        'order' => 1
    ];
});