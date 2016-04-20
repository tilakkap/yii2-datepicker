<?php
/**
 * @copyright Copyright (c) 2016 Thomas Hoppe
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace hoppe\datepicker;

use yii\web\AssetBundle;

/**
 * DatePickerLanguageAsset
 *
 * @author Thomas Hoppe
 * @package hoppe\datepicker
 */
class DatePickerLanguageAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/bootstrap-datepicker/dist/locales';

    public $depends = [
        'hoppe\datepicker\DateRangePickerAsset'
    ];
}
