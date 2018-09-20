# monkeylearn-php
Official PHP client for the MonkeyLearn API. Build and consume machine learning models for language processing from your PHP apps.

Autoload
--------

The first step to use `monkeylearn-php` is to download composer:

```bash
$ curl -s http://getcomposer.org/installer | php
```

Then we have to install our dependencies using:
```bash
$ php composer.phar install
```
Now we can use autoloader from Composer by:

```json
{
    "require": {
        "monkeylearn/monkeylearn-php": "~0.1"
    }
}
```

Or, if you don't want to use composer, clone the code and include this line of code:

    require 'autoload.php';


Usage examples
--------------

Here are some examples of how to use the library in order to create and use classifiers:
```php
require 'autoload.php';

// Use the API key from your account
$ml = new MonkeyLearn\Client('<YOUR API KEY HERE>');

// Create a new classifier
$res = $ml->classifiers->create('Test Classifier');

// Get the id of the new module
$model_id = $res->result['id'];

// Get the classifier detail
$res = $ml->classifiers->detail($model_id);

// Create two new tags on the classifier
$res = $ml->classifiers->tags->create($model_id, 'Negative');
$negative_id = $res->result['id'];
$res = $ml->classifiers->tags->create($model_id, 'Positive');
$positive_id = $res->result['id'];

// Now let's upload some data
$data = array(
    array('text' => 'The movie was terrible, I hated it.', 'tags' => [$negative_id]),
    array('text' => 'I love this movie, I want to watch it again!', 'tags' => [$positive_id])
);
$res = $ml->classifiers->upload_data($model_id, $data);

// Classify some texts
$res = $ml->classifiers->classify($model_id, ['I love the movie', 'I hate the movie']);
var_dump($res->result);
```

You can also use the sdk with extractors:

```php
require 'autoload.php';

$ml = new MonkeyLearn\Client('<YOUR API KEY HERE>');
$res = $ml->extractors->extract('<Extractor ID>', ['Some text to extract.']);
```
