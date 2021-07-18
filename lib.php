<?php

use mod_gharar\util;
use mod_gharar\moodle_vars;

util::forbid_access_if_not_from_moodle();

function gharar_add_instance($record)
{
    $moodle = moodle_vars::get_instance();

    $id = $moodle->get_database()->insert_record('gharar', $record);

    return $id;
}

function gharar_update_instance($record)
{
    $moodle = moodle_vars::get_instance();

    $id = $moodle->get_database()->update_record('gharar', $record);

    return true;
}

function gharar_delete_instance($recordId)
{
    $moodle = moodle_vars::get_instance();

    if (!$moodle->get_database()->get_record('gharar', ['id' => $recordId])) {
        return false;
    }

    if (!$moodle->get_database()->delete_records('gharar', ['id' => $recordId])) {
        $result = false;
    }
    return true;
}
