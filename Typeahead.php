<?php

namespace yiicms\widgets;


use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

class Typeahead extends InputWidget
{
    public $clientOptions = [];
    public $bloodhoundOptions = [];
    public $events = [];

    public $options = ['class' => 'form-control'];

    /**
     * @var string the hashed variable to store the pluginOptions
     */
    protected $hashVar;
    protected $hashBloodhoundVar;

    public function init()
    {
        parent::init();

    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    /**
     * Initializes bloodhound options
     */
    protected function initBloodhoundOptions()
    {
        $bloodhound = isset($this->bloodhoundOptions) ? $this->bloodhoundOptions : [];
        foreach ($bloodhound as $key => $value) {
            if (in_array($key, ['datumTokenizer', 'queryTokenizer']) && !$value instanceof JsExpression) {
                $bloodhound[$key] = new JsExpression($value);
            }
        }
        if (empty($bloodhound['datumTokenizer'])) {
            $bloodhound['datumTokenizer'] = new JsExpression("Bloodhound.tokenizers.obj.whitespace('value')");
        }
        if (empty($bloodhound['queryTokenizer'])) {
            $bloodhound['queryTokenizer'] = new JsExpression("Bloodhound.tokenizers.whitespace");
        }
        $this->bloodhoundOptions = $bloodhound;
    }

    /**
     * Initializes client options
     */
    protected function initClientOptions()
    {
        $this->initBloodhoundOptions();

        $options = $this->clientOptions;
        foreach ($options as $key => $value) {
            if (in_array($key, ['name', 'source']) && !$value instanceof JsExpression) {
                $options[$key] = new JsExpression($value);
            }
        }
        $this->clientOptions = $options;
    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript()
    {
        $js = '';
        $view = $this->getView();
        $this->initClientOptions();
        //$this->hashPluginOptions($view);

        $encBloodhoundOptions = empty($this->bloodhoundOptions) ? '{}' : Json::encode($this->bloodhoundOptions);
        $this->hashBloodhoundVar = 'typeahead_bloodhound_' . hash('crc32', $encBloodhoundOptions);
        $view->registerJs("var {$this->hashBloodhoundVar} = new Bloodhound({$encBloodhoundOptions});\n");
        $view->registerJs("{$this->hashBloodhoundVar}.initialize();\n");
        $id = $this->options['id'];
        if (empty($this->clientOptions['source'])) {
            $this->clientOptions['source'] = new JsExpression("{$this->hashBloodhoundVar}.ttAdapter()");
        }
        $encOptions = empty($this->clientOptions) ? '{}' : Json::encode($this->clientOptions);

        $events = '';
        foreach ($this->events as $event => $handler) {
            $events .= ".bind('{$event}', {$handler})";
        }

        $js .= '$("#' . $id . '")' . ".typeahead(null, {$encOptions}){$events};\n";
        TypeaheadAsset::register($view);
        $view->registerJs($js);
    }
}
