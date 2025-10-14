<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('root', new admin_category('mod_contractactivity', get_string('pluginname', 'mod_contractactivity')));

    $settingspage = new admin_externalpage(
        'mod_contractactivity_page',
        get_string('configpage', 'mod_contractactivity'),
        new moodle_url('/mod/contractactivity/admin/index.php'),
        'moodle/site:config'
    );

    $ADMIN->add('mod_contractactivity', $settingspage);
}
