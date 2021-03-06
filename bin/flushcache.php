<?php
/**
 * Flush de cache.
 */
use CsrDelft\Orm\Persistence\OrmMemcache;

chdir(dirname(__FILE__) . '/../lib/');

require_once 'configuratie.include.php';

if (OrmMemcache::instance()->getCache()->flush()) {
	echo 'Memcache succesvol geflushed';
} else {
	echo 'Memcache flushen mislukt';
	echo error_get_last()["message"];
}


