<?php
/**
 * Internationalisation for different language files
 *
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

/**
 * Include 1 file and simply translate what you need to translate. No superfluous features to slow you down.
 *
 * Instantiate like so:
 *      $i18n = new i18n('lang/json/en-au.json');
 *
 * Example usage:
 *      $i18n->_("Hello Liam");
 *      $greeting = $i18n->_r("Hello Liam");
 *      $i18n->_("Today is {0} the {1}", array(date('l'), date('jS')));
 *      $i18n->_("I have {0} apple{0|s} and {1} banana{1|s}", array(5, 1));
 */
class i18n
{
    /** @var array The final loaded language array that's accessed when calling _ and _r */
    private $language_array;

    /**
     * @param string $translation_file_path The path to the translation file
     * @return void
     */
    public function __construct($translation_file_path)
    {
        $this->loadFile($translation_file_path);
    }

    /**
     * echo the translated string, can include replacements for example:
     *      $i18n->_("Today is {0} the {1}", array(date('l'), date('jS')));
     *
     * @param string $english_phrase The english phrase to translate
     * @param array $replacements Replace values into the string
     * @return void
     */
    public function _($english_phrase, $replacements = null)
    {
        echo $this->_r($english_phrase, $replacements);
    }

    /**
     * Return the translated string, can include replacements for example:
     *      $i18n->_("Today is {0} the {1}", array(date('l'), date('jS')));
     *
     * @param string $english_phrase The english phrase to translate
     * @param array $replacements Replace values into the string
     * @return string
     */
    public function _r($english_phrase, $replacements = null)
    {
        if(!empty($this->language_array[$english_phrase])) {
            $parsed = $this->language_array[$english_phrase];
        } else {
            $parsed = $english_phrase;
        }

        preg_match_all('/\{(.*?)\}/', $english_phrase, $matches, PREG_SET_ORDER);

        // Replace values into string and handle plurals
        foreach($matches as $match_pair) {
            $separate = explode("|", $match_pair[1]);
            if(count($separate) > 1) {
                if(is_int($replacements[$separate[0]])) {
                    if($replacements[$separate[0]] != 1) {
                        $parsed = str_replace($match_pair[0], $separate[1], $parsed);
                    } else {
                        $parsed = str_replace($match_pair[0], "", $parsed);
                    }
                } else {
                    $parsed = str_replace($match_pair[0], "", $parsed);
                }
            } else {
                $parsed = str_replace($match_pair[0], $replacements[$match_pair[1]], $parsed);
            }
        }

        return $parsed;
    }

    /**
     * Load the translation file into the language_array
     * Set $this->language_array to the loaded translation file
     * @param string $translation_file_path The path to the translation file
     * @return void
     */
    private function loadFile($translation_file_path)
    {
        if(!file_exists($translation_file_path)) {
            throw new Exception($translation_file_path . ' not found.');
            return false;
        }

        $extension = substr(strrchr($translation_file_path, '.'), 1);

        switch ($extension) {
            case 'json':
                $file_contents = file_get_contents($translation_file_path);

                // Remove silly characters caused by some editors
                for ($i = 0; $i <= 31; ++$i) {
                    $file_contents = str_replace(chr($i), "", $file_contents);
                }
                $file_contents = str_replace(chr(127), "", $file_contents);
                if (0 === strpos(bin2hex($file_contents), 'efbbbf')) {
                   $file_contents = substr($file_contents, 3);
                }

                $this->language_array = json_decode($file_contents, true);
                break;

            case 'properties':
            case 'ini':
                $file_contents = file_get_contents($translation_file_path);
                $this->language_array = $this->parseIniFile($file_contents);
                break;

            // Filetype not supported
            default:
                $this->language_array = false;
                throw new Exception('.' . $extension . ' file type not supported');
                break;
        }
    }

    /**
     * Since parse_ini_file() does not support special characters like {} in the key,
     * we've got this ugly helper method
     * @param string $file_contents The file contents from loadFile function
     * @return array
     */
    private function parseIniFile($file_contents)
    {
        $final_language_array = array();
        $rows = explode(PHP_EOL, $file_contents);
        foreach($rows as $a_row) {
            $key_and_value = explode("=", $a_row);
            if(count($key_and_value) == 2) {
                $final_language_array[trim($key_and_value[0])] = trim($key_and_value[1]);
            }
        }
        return $final_language_array;
    }
}
