<?php
/**
 * @copyright Copyright (c) 2016 Thomas Hoppe
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace hoppe\datepicker;

use yii\web\AssetBundle;

/**
 * DateRangePickerAsset
 *
 * @author Thomas Hoppe
 * @package hoppe\datepicker
 */
class DateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@hoppe/datepicker/assets';

    public $css = [
        'css/bootstrap-daterangepicker.css'
    ];

    public $depends = [
        'hoppe\datepicker\DatePickerAsset'
    ];

}
