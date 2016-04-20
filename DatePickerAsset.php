<?php
/**
 * @copyright Copyright (c) 2016 Thomas Hoppe
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace hoppe\datepicker;

use yii\web\AssetBundle;

/**
 * DatePickerAsset
 *
 * @author Thomas Hoppe
 * @package hoppe\datepicker
 */
class DatePickerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/bootstrap-datepicker/dist';

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];

    public function init() {
        $this->css[] = YII_DEBUG ? 'css/bootstrap-datepicker3.css' : 'css/bootstrap-datepicker3.min.css';
        $this->js[] = YII_DEBUG ? 'js/bootstrap-datepicker.js' : 'js/bootstrap-datepicker.min.js';
    }
}
