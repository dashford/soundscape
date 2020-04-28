<?php

require_once 'web/app.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);