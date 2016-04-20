<?php
/**
 * @copyright Copyright (c) 2016 Thomas Hoppe
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace hoppe\datepicker;

use Yii;

/**
 * DatePickerTrait holds common attributes
 *
 * @author Thomas Hoppe
 * @package hoppe\datepicker
 */
trait DatePickerTrait
{
    /**
     * @var string the language to use
     */
    public $language;
    /**
     * @var array the options for the Bootstrap DatePicker plugin.
     * Please refer to the Bootstrap DatePicker plugin Web page for possible options.
     * @see http://bootstrap-datepicker.readthedocs.org/en/release/options.html
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the underlying Bootstrap DatePicker plugin.
     * Please refer to the [DatePicker](http://bootstrap-datepicker.readthedocs.org/en/release/events.html) plugin
     * Web page for possible events.
     */
    public $clientEvents = [];
    /**
     * @var string the size of the input ('lg', 'md', 'sm', 'xs')
     */
    public $size;
    /**
     * @var array HTML attributes to render on the container
     */
    public $containerOptions = [];

    public $disabled;

    /**
     * Yii i18n messages configuration for generating translations
     *
     * @param string $dir the directory path where translation files will exist
     * @param string $cat the message category
     *
     * @return void
     */
    public function initI18N($dir = '', $cat = '')
    {
        if (empty($cat)) {
            return;
        }

        if (empty($dir)) {
            $reflector = new \ReflectionClass(get_class($this));
            $dir = dirname($reflector->getFileName());
        }
        Yii::setAlias("@{$cat}", $dir);

        $i18n = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'basePath'         => "@{$cat}/messages",
            'forceTranslation' => true,
        ];
        Yii::$app->i18n->translations["{$cat}*"] = $i18n;
    }
}