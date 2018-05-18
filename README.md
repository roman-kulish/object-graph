# Object Graph 

[![Build Status](https://travis-ci.org/roman-kulish/object-graph.svg?branch=master)](https://travis-ci.org/roman-kulish/object-graph)

## Table of Contents

* [Context](#context)

### Context

Context is used to pass external variables to field resolvers:
* the Root object will receive a copy of the global Context; and
* its nested objects will receive a copy of the Root object Context

> Note: Context is designed to contain scalar variables and does not support deep cloning. If there is an object 
stored in the Context, then objects in cloned and original Contexts will retain references to each other.

There is a JSON with language specific greetings and in our Object Graph Model we'd like to use 
Australian English everywhere:

```json
{
  "greetings": {
    "en-us": "Hi! How are you doing?",
    "en-au": "G'day! How are you going? "
  }
}
``` 

The desired locale can be set on the Context and then accessed in the field resolver:

```php
<?php

$context = new Context();
$context['locale'] = 'en-au';

$og = new ObjectGraph($context);
$model = $og->resolveObject($json);

echo $model->sayHi;
```

and within the "sayHi" field resolver:

```php
<?php
// defining new field and its resolver in Schema
$config
  ->addField("sayHi", Type::STRING)
  ->withResolver(function(stdClass $data, Context $context) {
    $locale = $context['locale'];
    $fallbackLocale = 'en-us';

    return (isset($data->greetings->$locale) ? 
      $data->greetings->$locale : 
      $data->greetings->$fallbackLocale);
  });
```

