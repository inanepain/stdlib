<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\stdlib
 * @category stdlib
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Stdlib\Object;

/**
 * LogTrait - getLog()
 *
 * Easy log access
 *
 * @since 0.4.6
 *
 * @version 1.0.1
 */
trait LogTrait {
    /*
    CREATE TABLE `logs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `priority` int(11) unsigned DEFAULT NULL,
        `priorityName` varchar(10) DEFAULT NULL,
        `timestamp` datetime DEFAULT NULL,
        `message` text,
        `extra_route` varchar(100) DEFAULT NULL,
        `extra_user_id` int(11) unsigned DEFAULT NULL,
        `extra_session` varchar(15) DEFAULT NULL,
        `extra_ip_address` varchar(20) DEFAULT NULL,
        `extra_file` varchar(200) DEFAULT NULL,
        `extra_line` int(11) unsigned DEFAULT NULL,
        `extra_class` varchar(45) DEFAULT NULL,
        `extra_function` varchar(20) DEFAULT NULL,
        `extra_options` text,
        PRIMARY KEY (`id`)
    );
    */

    /*
    'factories' => [
        'LogService' => function ($sm) {
            $logger = new Logger();
            $priority = new Priority(getenv('LOG_LEVEL') ?: $logger::WARN);

            $fileWriterGeneral = new Stream('log/general.log');

            $dbWriter = new Db(GlobalAdapterFeature::getStaticAdapter(), 'logs');
            $dbWriter->setFormatter(new \Laminas\Log\Formatter\Db('Y-m-d H:i:s'));

            $fileWriterGeneral->addFilter($priority);
            $dbWriter->addFilter($priority);

            $logger->addWriter($fileWriterGeneral);
            $logger->addWriter($dbWriter);

            return $logger;
        },
    ]
    */

    /**
     * The log service
     */
    protected  \Laminas\Log\Logger $logService;

    /**
     * Get Log Service
     *
     * @return \Laminas\Log\Logger the logger
     */
    public function getLog(): \Laminas\Log\Logger {
        if (!isset($this->logService))
            $this->logService = $this->getEvent()->getApplication()->getServiceManager()->get('LogService');

        return $this->logService;
    }
}
