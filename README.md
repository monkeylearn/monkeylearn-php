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
$module_id = $res->result['classifier']['hashed_id'];

// Get the id of the root node
$res = $ml->classifiers->detail($module_id);
$root_id = $res->result['sandbox_categories'][0]['id'];

// Create two new categories on the root node
$res = $ml->classifiers->categories->create($module_id, 'Negative', $root_id);
$negative_id = $res->result['category']['id'];
$res = $ml->classifiers->categories->create($module_id, 'Positive', $root_id);
$positive_id = $res->result['category']['id'];

// Now let's upload some samples
$samples = array(
    array('The movie was terrible, I hated it.', $negative_id), 
    array('I love this movie, I want to watch it again!', $positive_id)
);
$res = $ml->classifiers->upload_samples($module_id, $samples);

// Now let's train the module!
$res = $ml->classifiers->train($module_id);

// Classify some texts
$res = $ml->classifiers->classify($module_id, ['I love the movie', 'I hate the movie'], true);
var_dump($res->result);
```

You can also use the sdk with extractors and pipelines:
    
```php
require 'autoload.php';

$ml = new MonkeyLearn\Client('<YOUR API KEY HERE>');
$res = $ml->extractors->extract('<Extractor ID>', ['Some text for the extractor.']);
$res = $ml->pipelines->run('<Pipeline ID>', {'input':[{'text': 'some text for the pipeline.'}]}, false);
```
