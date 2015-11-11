# yii2-typeahead
Typeahead widget for Yii2 framework.

```php
            <?php echo Typeahead::widget([
                'id' => 'typeahead-id',
                'name' => 'typeahead-name',
                'clientOptions' => [
                    'displayKey' => 'value',
                    'templates' => [
                        'suggestion' => new JsExpression("function(data){ return '<p>' + data.value + '</p>'; }"),
                    ],
                ],
                'events' => [
                    'typeahead:selected' => new JsExpression(
                        'function(obj, datum, name) { window.location = datum.url; }'
                    ),
                ],
                'bloodhoundOptions' => [
                    'remote' => [
                        'url' => Url::to(['controller/action']) . '?q=%QUERY',
                        'ajax' => ['data' => [
                            'id' => $model->id,
                        ]],
                        'replace' => new JsExpression("
                            function(url, query) {
                                return url.replace('%QUERY', query);
                            }
                        "),
                        'filter' => new JsExpression("
                            function(list) {
                                return $.map(list, function(item) { return { value: item.name, url: item.url }; });
                            }
                        "),
                    ],
                ],
                'options' => ['class' => 'form-control', 'placeholder' => 'Title', 'autofocus' => true],
            ]);
```

