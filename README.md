DatePicker
==========
DatePicker Thai BE pjax ready

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist hoppe/yii2-datepicker "*"
```

or add

```
"hoppe/yii2-datepicker": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= DatePicker::widget([
    'type'          => DatePicker::TYPE_BUTTON,
    'value'         => '2001-01-08',
    'options' =>[
        'class'=>'btn btn-link',
        'label'=> '<span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>',
    ],
    'clientOptions' => [
        'format' => 'yyyy-mm-dd',
        'autoclose'   => true,
        'minViewMode' => 1,
        'endDate' => 'new Date();',
        'startDate' => '-2y',

    ],
    'clientEvents' => [
        "changeDate" => "function(e) {
            document.location.href = '".Url::to(['/site/index'])."?from_date=' + e.format();
        }",

    ]
]);
?>```