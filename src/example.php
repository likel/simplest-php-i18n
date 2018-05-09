<?php
/**
 * Usage:
 *      $i18n = new i18n('lang/json/en-au.json');
 *      $i18n->_("Hello Liam"); // echoes G'day Liam
 *
 * @package     simplest-php-i18n
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2018 Liam Kelly
 * @license     MIT License <https://github.com/likel/simplest-php-i18n/blob/master/LICENSE>
 * @link        https://github.com/likel/simplest-php-i18n
 * @version     1.0.0
 */
require_once("i18n.php");

$i18n = new i18n('lang/json/en-au.json');

$i18n->_("Hello Liam");
$i18n->_("Today is {0} the {1}", array(date('l'), date('jS')));
$i18n->_("I have {0} apple{0|s} and {1} banana{1|s}", array(5, 1));
