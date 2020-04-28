<?php

require_once 'config/doctrine-config.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);