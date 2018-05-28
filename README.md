# Object Graph

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg)](https://php.net/)
[![Build Status](https://travis-ci.org/roman-kulish/object-graph.svg?branch=master)](https://travis-ci.org/roman-kulish/object-graph)
[![Code Coverage](https://img.shields.io/codecov/c/github/roman-kulish/object-graph.svg)](https://codecov.io/gh/roman-kulish/object-graph)

## Introduction

Object Graph wraps a plain PHP object (e.g., JSON object) and exposes a value object (a GraphNode instance) with a predefined set of properties.

Object Graph can also be used to introduce compatibility between different versions of a JSON payload and produce a GraphNode with the common set of properties which suits both payloads.

> This library a is written-from-the-ground version of a project initially developed for the NewsCorp Australia. Kudos to Juan Zapata (@juankk), Salvatore Balzano (@salvo1404) 
and Michael Chan (@michaelChanNews) for their time and contribution to this library. 

## Table of Contents

* [Walk through](#walk-through)
  * [Model](#model)
  * [Schema](#schema)
    * [Strict Schema](#strict-schema)
    * [A word on nested objects and field resolvers](#a-word-on-nested-objects-and-field-resolvers)
  * [Resolver](#resolver)
  * [Context](#context)

### Walk through

Let's jump straight to the business. Imaging that we have two versions of a payload from our User API:

**/v1/user/123:**
```json
{
  "userName": "Arnold Schwarzenegger",
  "dob": "1947-07-30",
  "emailAddress": "arnold.schwarzenegger@gov.ca.gov"
}
```

**/v2/user/123:**
```json
{
  "firstName": "Arnold",
  "lastName": "Schwarzenegger",
  "dateOfBirth": "1947-07-30",
  "email": "arnold.schwarzenegger@gov.ca.gov"
}
```
Let's create a User model with this set of properties, build a Schema for each payload version and, finally, build a resolver which will automatically apply corresponding Schema to a particular payload version and produce us a User mode.

* **Model**: a instance of a GraphNode, that represents a plain PHP object, where the object fields are defined as Model properties;
* **Schema**: contains a list of Model properties / fields; resolvers, default values, PHP type casting configuration for each field and more;
* **Resolver**: performs magic and builds a Model from a plain PHP object;
* **Context**: allows to share variables between feilds resolvers.

These are the main components of the ObjectGraph library.

#### Model

First we start with modeling what properties our Model should have:
```php
<?php
/**
 * @property string   $firstName
 * @property string   $lastName
 * @property string   $fullName
 * @property DateTime $dateOfBirth
 * @property string   $email
 * @property string   $schema      Payload version
 */
class User extends GraphNode
{
    const SCHEMA_V1 = 'v1';
    const SCHEMA_V2 = 'v2';
}

```

A Model class must extend `GraphNode` and list properties in the class header docblock. This will enabled IDE auto-suggestion as well.

> Note that in this release, Model is immutable and an attempt to set or unset a value on the Model will throw an exception.

Your Model class can extend another Model class, also contain API, constants as a normal PHP class, which it really is.

Model properties can be accessed as PHP object properties `$user->dateOfBirth` or as array elements 
`$article['heraldsun.com.au']->titleOverride`.

Model API:
* `Model::getData()`, returns original underlying plain PHP object
* `Model::asArray()`, transforms model to an array, according to the fields defined on a Schema;
* `Model::asObject()`, transforms model to an object, according to the fields defined on a Schema;

#### Schema

Next step, we are going to define a schema for each of the payload version. A schema class must extend `Schema`.

There are a few useful methods in a `Schema` you can or may wish to override:
* `Schema::getGraphNodeClassName()`, this method must return a model class name, in our case it is `User`.
* `Schema::isStrict()`, must return a boolean flag which indicates whether this schema is strict or not. See below.
* `Schema::build()`, adding schema fields and potentially other configuration work must happen inside this method, which acts as a custom class `__constructor` for the `Schema` class.

##### Strict Schema

When a schema is strict, then you can only access Model fields defined on a `Scheme` instance. Trying to access a field which exists on a data source, but not defined on a schema, will give you NULL.

When transforming a Model to an array or an object, the resulting data will contain only fields defined on a strict schema. If schema is not strict then the resulting data will contain a combination of all fields defined on the source object and schema.

By default the Schema **is not strict**.

**Version 1**
```php
<?php
class UserSchemaV1 extends Schema
{
    public function getGraphNodeClassName(): string
    {
        return User::class; // the Resolver must use User model class
    }

    public function isStrict(): bool
    {
        return true; // this is a strict schema
    }

    protected function build(SchemaBuilder $schema)
    {

        /**
         * Use $schema to define fields: addField() returns an instance of the FieldBulder class
         */

        $schema->addField('firstName')->withResolver(function (stdClass $data) {
            if (empty($data->userName)) {
                return null;
            }

            $name = preg_split('/\s+/', $data->userName, 2);

            return $name[0];
        });

        /**
         * Field resolver function is the most powerful way to extract data from the data source.
         */

        $schema->addField('lastName')->withResolver(function (stdClass $data) {
            if (empty($data->userName)) {
                return null;
            }

            $name = preg_split('/\s+/', $data->userName, 2);

            return (sizeof($name) === 2 ? $name[1] : null);
        });

        /**
         * Defining a field aliase allows to avoid using resolver. This code below tells that, 
         * there is a Model property "fullName", which must receive data from the source object 
         * field named "userName".
         */

        $schema->addField('fullName')->asAliasOf('userName');

        /**
         * Additionally, a type of resulting value can be specified on a field
         */

        $schema->addField('dateOfBirth')->asAliasOf('dob')->asScalarValue(ScalarType::DATE_TIME);
        $schema->addField('email')->asAliasOf('emailAddress');

        /**
         * You can specify a default value for an existing field or define a "virtual" 
         * field with the default value.
         */

        $schema->addField('schema')->withDefaultValue(User::SCHEMA_V1);
    }
}
```

**Version 2**
```php
<?php
class UserSchemaV2 extends Schema
{
    public function getGraphNodeClassName(): string
    {
        return User::class;  // the Resolver use User model class
    }

    public function isStrict(): bool
    {
        return true; // this is a strict schema
    }

    protected function build(SchemaBuilder $schema)
    {

        /**
         * If source object field name and Model property name match and you are happy with 
         * the type of the source value, this is what you only need to register a field.
         */

        $schema->addField('firstName');
        $schema->addField('lastName');

        $schema->addField('fullName')->withResolver(function (stdClass $data) {
            if (empty($data->firstName) || empty($data->lastName)) {
                return null;
            }

            return sprintf('%s %s', $data->firstName, $data->lastName);
        });

        $schema->addField('dateOfBirth')->asScalarValue(ScalarType::DATE_TIME);
        $schema->addField('email');
        $schema->addField('schema')->withDefaultValue(User::SCHEMA_V2);
    }
}
```

##### A word on nested objects and field resolvers

The first argument a field resolver function receives is always a parent object the field belongs to.

As an example, there is a source PHP object:
```text
User {
  "social": SocialIntegration {
    "facebook" { ... }
  }
}
```

* `social` field resolver function will receive the root object;
* `facebook` field resolver function will receive the object assigned to `social`

Each nested object can have its own `Schema`, which can define a custom Model class to use.

#### Resolver

We can atually start using the above right away:

```php
$resolver = new Resolver();

$model1 = $resolver->resolveObject($userPayloadV1, UserSchemaV1::class);
$model2 = $resolver->resolveObject($userPayloadV2, UserSchemaV2::class);

echo $model1->firstName; // outputs "Arnold"
echo $model2->firstName; // outputs "Arnold"
```

However, it is not fun, because we still need to decide which schema to use with the data. Let's automate it:
```php
<?php
class UserResolver extends Resolver
{

    /**
     * Let's override Resolver::resolveObject() in our own resolver and
     * make it inspect the raw source object to decide which schema version 
     * to use
     */
  
    public function resolveObject(
        stdClass $data = null,
        string $schemaClassName = null,
        Context $context = null
    ): GraphNode {
        if (empty($data)) {
            return null;
        }

        switch (true) {
            case (
                  isset($data->fullName) || 
                  isset($data->dob) || 
                  isset($data->emailAddress)
            ): // it is definitely a v1 User payload
                $schemaClassName = UserSchemaV1::class; 
                break;

            case (
                (isset($data->firstName) && isset($data->lastName)) ||
                isset($data->emailName) ||
                isset($data->dateOfBirth)
            ): // it is clearly a v2 User payload
                $schemaClassName = UserSchemaV2::class; 
                break;

            default:
                throw new ObjectGraphException('Unable to detect schema from the user data object');
        }

        /**
         * Call parent::resolveObject() with a Schema class we have just detected
         */

        return parent::resolveObject($data, $schemaClassName, $context);
    }
}
```

And try now:

```php
$resolver = new Resolver();
$model    = $resolver->resolveObject($anyUserPayload);

echo $model->firstName; // Woo Hoo!! It still outputs "Arnold"!
```

#### Context

Context is used to pass external variables to field resolvers:
* the Root object will receive a copy of the global Context; and
* its nested objects will receive a copy of the Root object Context

> Note: Context is designed to contain scalar variables and does not support deep cloning. If there is an object 
stored in the Context, then when Context is cloned, both cloned and original Contexts will retain references to each other.

There is a JSON with language specific greetings and in our Object Graph Model we'd like to use 
Australian English where it is possible:

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
$schema
  ->addField("sayHi")
  ->withResolver(function(stdClass $data, Context $context) {
    $locale = $context['locale']; // get locale from the Context
    $fallbackLocale = 'en-us';

    return (isset($data->greetings->$locale) ? 
      $data->greetings->$locale : 
      $data->greetings->$fallbackLocale);
  });
```
