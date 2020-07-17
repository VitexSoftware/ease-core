<?php

/**
 * Logging trait
 * 
 * @category Logging
 * 
 * @author    Vitex <vitex@hippy.cz>
 * @copyright 2019 Vitex@hippy.cz (G)
 * @license   https://opensource.org/licenses/MIT MIT
 * 
 * PHP 7
 */

namespace Ease\Logger;

/**
 *
 * @author vitex
 */
trait Logging {

    /**
     * Objekt pro logování.
     *
     * @var Logger\Regent
     */
    public $logger = null;

    /**
     * Add message to stack to show or write to file
     * Přidá zprávu do zásobníku pro zobrazení uživateli inbo do logu.
     *
     * @param string $message text zpravy
     * @param string $type    fronta
     * @param string $caller  Message source name
     * 
     * @return boolean message added
     */
    public function addStatusMessage($message, $type = 'info', $caller = null) {
        return $this->getLogger()->addToLog(is_null($caller) ? get_class($this) : $caller, $message, $type);
    }

    /**
     * Obtain global status messages
     *
     * @return array
     */
    public function getStatusMessages() {
        return $this->getLogger()->getMessages();
    }

    /**
     * Erase all status messages
     * 
     * @return type
     */
    public function cleanSatatusMessages() {
        return $this->getLogger()->cleanMessages();
    }

    /**
     * Provide logger object
     * 
     * @param string|array $options
     * 
     * @return Logger\Regent
     */
    public function getLogger($options = null) {
        if (is_null($this->logger)) {
            $this->logger = Regent::singleton($options);
        }
        return $this->logger;
    }

    /**
     * Add Info about used PHP and EasePHP Library
     *
     * @param string $prefix banner prefix text
     * @param string $suffix banner suffix text
     */
    public function logBanner($prefix = null, $suffix = null) {
        $this->addStatusMessage(
                trim($prefix . ' PHP v' . phpversion() . ' EasePHP Framework v' . \Ease\Atom::$frameworkVersion . ' ' . $suffix),
                'debug'
        );
    }

}
