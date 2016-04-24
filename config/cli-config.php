<?php

use \BigCommerce\Bootstrap;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);
return Bootstrap::doctrineHelserSet();