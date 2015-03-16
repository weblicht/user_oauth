<?php

OCP\User::checkAdminUser();

$param = 'introspectionEndpoint';

if($_POST) {
    if (isset($_POST[$param])) {
        OCP\Config::setAppValue('user_oauth', $param, $_POST[$param]);
    }
}

$tmpl = new OCP\Template( 'user_oauth', 'settings');


$tmpl->assign('introspectionEndpoint', OCP\Config::getAppValue('user_oauth', 'introspectionEndpoint', 'https://frko.surfnetlabs.nl/workshop/php-oauth/introspect.php' ));

return $tmpl->fetchPage();

