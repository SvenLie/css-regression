css-regression
==============
CSS Regression tests in Codeception

Install
-------
```console
$ composer require --dev svenlie/css-regression:dev-master
```

Configure
---------
```yaml
modules:
    enabled:
        - WebDriver:
            ...
        - \svenlie\CodeceptionCssRegression\Module\CssRegression:
            referenceImageDirectory: 'referenceImages'
            failImageDirectory: 'failImages'
            maxDifference: 0.1
            automaticCleanup: true
            module: WebDriver
            fullScreenshots: false
```


Usage
-----
```php
$I->amOnPage('/');
$I->hideElements('.socialMediaButton');
$I->seeNoDifferenceToReferenceImage('NewsArticle', '#news-article');
```

