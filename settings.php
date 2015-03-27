<?php

OCP\User::checkAdminUser();

$params = array('introspectionEndpoint', 'username', 'password');

if($_POST) {
    foreach($params as $param) {
        if (isset($_POST[$param])) {
            OCP\Config::setAppValue('user_oauth', $param, $_POST[$param]);
        }
    }
}

$tmpl = new OCP\Template('user_oauth', 'settings');


$tmpl->assign('introspectionEndpoint', OCP\Config::getAppValue('user_oauth', 'introspectionEndpoint', 'https://frko.surfnetlabs.nl/workshop/php-oauth/introspect.php' ));
$tmpl->assign('username', OCP\Config::getAppValue('user_oauth', 'username'));
$tmpl->assign('password', OCP\Config::getAppValue('user_oauth', 'password'));

return $tmpl->fetchPage();

