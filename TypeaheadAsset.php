<?php

namespace yiicms\widgets;


use yii\web\AssetBundle;

class TypeaheadAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/typeahead.js/dist';

    /**
     * @inheritdoc
     */
    public $css = [
        //'css/typeaheadjs.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'bloodhound.min.js',
        'typeahead.jquery.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'common\widgets\typeahead\TypeaheadBootstrapAsset',
    ];
}
