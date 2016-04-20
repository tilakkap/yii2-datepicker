<?php
/**
 * @copyright Copyright (c) 2016 Thomas Hoppe
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace hoppe\datepicker;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * DatePicker renders a DatePicker input.
 *
 * @author Thomas Hoppe
 * @package hoppe\datepicker
 */
class DatePicker extends InputWidget
{
    const CALENDAR_ICON = '<i class="glyphicon glyphicon-calendar"></i>';
    const TYPE_INPUT = 1;
    const TYPE_COMPONENT = 7;
    /**
     * whether to render the input as an inline calendar
     */
    const TYPE_INLINE = 4;
    const TYPE_BUTTON = 5;
    const TYPE_LINK = 6;

    use DatePickerTrait;

    /**
     * @var string the template to render the input.
     */
    public $template;
    /**
     * @var mixed the calendar picker button configuration.
     * - if this is passed as a string, it will be displayed as is (will not be HTML encoded).
     * - if this is set to `false`, the picker button will not be displayed.
     * - if this is passed as an array (this is the DEFAULT) it will treat this as HTML attributes for the button (to
     *     be displayed as a Bootstrap addon). The following special keys are recognized;
     *   - icon: string, the bootstrap glyphicon name/suffix. Defaults to 'calendar'.
     *   - title: string|bool, the title to be displayed on hover. Defaults to 'Select date & time'. To disable, set it
     *     to `false`.
     */
    public $pickerButton = [];

    /**
     * @var mixed the calendar remove button configuration - applicable only for type set to
     *     `DatePicker::TYPE_COMPONENT_PREPEND` or `DatePicker::TYPE_COMPONENT_APPEND`.
     * - if this is passed as a string, it will be displayed as is (will not be HTML encoded).
     * - if this is set to `false`, the remove button will not be displayed.
     * - if this is passed as an array (this is the DEFAULT) it will treat this as HTML attributes for the button (to
     *     be displayed as a Bootstrap addon). The following special keys are recognized;
     *   - icon - string, the bootstrap glyphicon name/suffix. Defaults to 'remove'.
     *   - title - string, the title to be displayed on hover. Defaults to 'Clear field'. To disable, set it to
     *     `false`.
     */
    public $removeButton = [];


    public $type = self::TYPE_INPUT;

    private $inputType = 'textInput';
    private $inputTypeArgs = [];
    private $_clientEvents = [];
    private $_hasHiddenField = false;
    private $_js;
    public $insidePjax = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_hasHiddenField = $this->type == self::TYPE_BUTTON || $this->type == self::TYPE_LINK || $this->type == self::TYPE_INLINE;

        Html::addCssClass($this->options, 'form-control');
        if ($this->type == self::TYPE_INLINE) {
            if (empty($this->options['readonly'])) {
                $this->options['readonly'] = true;
            }
            Html::addCssClass($this->options, 'text-center');
        }
        if ($this->_hasHiddenField) {
            $this->inputType = 'hiddenInput';
        }

        if ($this->hasModel()) {
            $this->inputType = 'active' . ucfirst($this->inputType);
            $this->inputTypeArgs = [&$this->model, &$this->attribute, &$this->options];
        } else {
            $this->inputTypeArgs = [&$this->name, &$this->value, &$this->options];
        }

    }

    /**
     * Parses the input to render based on markup type
     *
     * @param string $input
     *
     * @return string
     */
    protected function parseMarkup()
    {
        if ($this->size) {
            Html::addCssClass($this->options, 'input-' . $this->size);
            Html::addCssClass($this->containerOptions, 'input-group-' . $this->size);
        }

        if ($this->disabled) {
            Html::addCssClass($this->options, 'disabled');
            Html::addCssClass($this->containerOptions, 'disabled');
        }
        Html::addCssClass($this->options, 'form-control');
        Html::addCssClass($this->containerOptions, 'date');

        switch ($this->type) {
            case self::TYPE_INPUT:
                $component = strtr($this->template, [
                    '{input}' => $this->getInput(),
                ]);
                return $component;
            case self::TYPE_BUTTON:
                Html::removeCssClass($this->options, ['form-control', 'input-' . $this->size]);
                Html::removeCssClass($this->containerOptions, 'input-group-' . $this->size);
                $label = ArrayHelper::remove($this->options, 'label', self::CALENDAR_ICON);
                $component = strtr($this->template, [
                    '{button}' => Html::button($label, $this->options),
                    '{input}'  => $this->getInput(),
                ]);
                return Html::tag('span', $component, $this->containerOptions);
            case self::TYPE_INLINE:
                $component = strtr($this->template, [
                    '{input}' => $this->getInput(),
                ]);
                return Html::tag('div', $component, $this->containerOptions);
            case self::TYPE_COMPONENT:
                $input = $this->getInput();
                Html::addCssClass($this->containerOptions, "input-group");
                $component = strtr($this->template, [
                    '{picker}' => $this->renderAddOn($this->pickerButton),
                    '{remove}' => $this->renderAddOn($this->removeButton, 'remove'),
                    '{input}'  => $input,
                ]);
                return Html::tag('div', $component, $this->containerOptions);

            default:
                return '';
        }
    }

    /**
     * Renders the date picker widget
     */
    protected function renderDatePicker()
    {
        $this->initI18N(__DIR__, 'htdate');
        if (empty($this->template)) {
            switch ($this->type) {
                case self::TYPE_INPUT:
                    $this->template = '{input}';
                    break;
                case self::TYPE_BUTTON:
                    $this->template = '{button}{input}';
                    break;
                case self::TYPE_INLINE:
                    $this->template = '{input}';
                    break;
                case self::TYPE_COMPONENT:
                    $this->template = '{picker}{remove}{input}';
                    break;
                default:
                    $this->template = '{input}';
            }
        }
        return $this->parseMarkup();
    }

    /**
     * Returns the addon to render
     *
     * @param array $options the HTML attributes for the addon
     * @param string $type whether the addon is the picker or remove
     *
     * @return string
     */
    protected function renderAddOn(&$options, $type = 'picker')
    {
        if ($options === false) {
            return '';
        }
        if (is_string($options)) {
            return $options;
        }
        $title = ArrayHelper::remove($options, 'title', Yii::t('htdate', ($type === 'picker') ? 'Select date' : 'Clear field'));
        $icon = Html::tag('span', '', ['class' => ArrayHelper::remove($options, 'icon', ($type === 'picker') ? 'glyphicon glyphicon-calendar' : 'glyphicon glyphicon-remove')]);


        $options['title'] = $title;
        Html::addCssClass($options, 'input-group-addon ht-' . $type);
        return Html::tag('span', $icon, $options);
    }

    private function getInput()
    {
        return call_user_func_array('yii\helpers\Html::' . $this->inputType, $this->inputTypeArgs);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->renderDatePicker();
        $this->registerClientScript();
    }

    /**
     * Registers required script for the plugin to work as DatePicker
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        if ($this->language === null) {
            $this->language = Yii::$app->language;
        }

        if ($this->language !== null) {
            $this->clientOptions['language'] = $this->language;

            $mainLanguage = substr($this->language, 0, 2);

            if ($mainLanguage === 'th') {
                DateRangePickerAsset::register($view)->js[] = 'js/bootstrap-datepicker-B.E.js';
            }

            $path = Yii::getAlias(DatePickerLanguageAsset::register($view)->sourcePath);

            if (is_file($path . DIRECTORY_SEPARATOR . 'bootstrap-datepicker.' . $mainLanguage . '.min.js')) {
                DatePickerLanguageAsset::register($view)->js[] = 'bootstrap-datepicker.' . $mainLanguage . '.min.js';
            } elseif (is_file($path . DIRECTORY_SEPARATOR . 'bootstrap-datepicker.' . $this->language . '.min.js')) {
                DatePickerLanguageAsset::register($view)->js[] = 'bootstrap-datepicker.' . $this->language . '.min.js';
            }

        } else {
            DatePickerAsset::register($view);
        }


        $selector = "jQuery('#" . $this->options['id'] . "')";
        if ($this->_hasHiddenField or $this->type === self::TYPE_COMPONENT) {
            $selector .= ".parent()";
            $this->_clientEvents['changeDate'] = "function (e){ jQuery('input#" . $this->options['id'] . "').val(e.format());}";
        }

        $options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '';
        $this->_js = ";$selector.datepicker($options)";


        foreach ($this->clientEvents as $event => $handler) {
            $this->_js .= ".on('$event', $handler)";
        }

        foreach ($this->_clientEvents as $event => $handler) {
            $this->_js .= ".on('$event', $handler)";
        }
        $this->_js .= ";" . $selector . ".find('.ht-remove').click(function(event){" . $selector . ".datepicker('hide');jQuery('input#" . $this->options['id'] . "').val('');})";

        // will fire on initial page load, and subsequent PJAX page loads
        $view->registerJs('jQuery(document).on("ready' . ($this->insidePjax ? ' pjax:end' : '') . '", function() {' . $this->_js . '});', $view::POS_END);
    }
}
