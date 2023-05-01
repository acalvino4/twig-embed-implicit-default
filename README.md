# Twig Embed with Implicit Default Block

> **Warning**
> This extension introduces a breaking change to twig. However, it will most likely not affect too many of your files, is probably quite easy to work around, and is most definitely worth it. See [details](#breaking-change).

1. [Rationale](#rationale)
1. [Usage](#usage)
    - [Details](#notes)
    - [Breaking Change](#breaking-change)
1. [Getting Started](#getting-started)
    - [Craft CMS](#craft-cms)

## Rationale

In vanilla twig, to override a slot of a parent template via an embed, we need to declare a block.

```twig
{% embed 'wrapper.twig' %}
  {% block content %}
    <p>This is default block content</p>
  {% endblock %}
{% endembed %}

{# example wrapper.twig #}
<div class='bg-slate-300'>
  <div class='max-w-7xl py-16 '>
    {% block content %}{% endblock %}
  </div>
</div>
```

When following best practices to keep code modular and dry, you'll typically create components for various code chunks, but this results in rather bloated and unreadable markup, especially as you start to nest (and this is only two levels!):

```twig
{% embed 'wrapper.twig' %}
  {% block default %}
    <p>
      This is a CTA, so naturally it includes a
      {% embed 'button.twig' %}
        {% block default %}Button{% endblock %}
      {% endembed %}
    </p>
  {% endblock %}
{% endembed %}
```

The explicit block declarations are handy for extending layouts (where you'll need 'header', 'footer', and 'sidebar', for example), but in most cases, `embed` is probably just being used to include components where you need to pass in some content, and for _these_ cases, typically only one content slot is required.

So we've sacrificed simplicity of the base case for greater flexibility ... but what if we could have both?

## Usage

This twig extension allows the use of an implicit default block in embeds:

```twig
{% embed 'wrapper.twig' %}
  <p>This is default block content</p>
{% endembed %}

{# same as this, which still will work #}
{% embed 'my_subtemplate.twig' %}
  {% block default %}
    <p>This is default block content</p>
  {% endblock %}
{% endembed %}
```

The longer example above could become

```twig
{% embed 'wrapper.twig' %}
    <p>
      This is a CTA, so naturally it includes a
      {% embed 'button.twig' %}Button{% endembed %}
    </p>
{% endembed %}
```

To specify where that default content goes in the component template, follow the `wrapper.twig` example above, or, if your default block has no/simple default content, you can use twig's [block shortcut syntax](https://twig.symfony.com/doc/3.x/tags/extends.html#block-shortcuts):

```twig
{% block default %}{% endblock %}
{# same as #}
{% block default '' %}
```

This works because all this extension does is parse the initial content within the embed into a block named 'default'.

### Notes

The implicit default content can be combined with (any number of) named blocks:

```twig
{% embed 'wrapper.twig' %}
  <p>This is default block content</p>
  {% block special_slot %}
    Special content
  {% endblock %}
{% endembed %}

{# example wrapper.twig #}
<div class='bg-slate-300'>
  <div class='max-w-7xl py-16 '>
    {% block default '' %}
    {% block special_slot '' %}
  </div>
</div>
```

The implicit default content needs to be placed _before_ any explicit blocks (See [this issue](https://github.com/acalvino4/twig-embed-implicit-default/issues/1)):

```twig
{# Will throw error: "A template that extends another one cannot include content outside Twig blocks." #}
{% embed 'wrapper.twig' %}
  {% block special_slot %}
    Special content
  {% endblock %}
  <p>This is default block content</p>
{% endembed %}
```

If you have both implicit default content and an explicit default block, an error will be thrown (just as when you declare two blocks with the same name):

```twig
{# Will throw error: "The block 'default' has already been defined" #}
{% embed 'wrapper.twig' %}
  <p>This is default block content</p>
  {% block default %}
    Other default content???
  {% endblock %}
{% endembed %}
```

### Breaking Change

Twig, by default, does not allow content _that outputs_ within `embed` tags but outside `block` tags. In this respect, this extension doesn't change any defined behavior; it only defines what previously was an error. However, twig does allow _non-outputting content_ in such a context, for example:

```twig
{% embed 'wrapper.twig' %}
  {% set a = 'foo bar' %}
  {% block content %}
    <p>{{ a }}</p>
  {% endblock %}
{% endembed %}
```

Using this extension, that `set` tag would get absorbed as part of the default block, and `a` would be undefined in the following block.

In this case, the solution is simple: move the `set` tag outside the `embed` scope. I suspect most instances of this change will be equally straightforward to adapt for this extension. If you run into something more complex, please file an issue and I will try to deal with or document that case.

## Getting Started

```sh
composer require acalvino4/twig-embed-implicit-default
```

Then register the extension as appropriate for your framework.

- [Vanilla](https://twig.symfony.com/doc/3.x/advanced.html#extending-twig)
- [Craft CMS](https://craftcms.com/docs/4.x/extend/extending-twig.html#register-a-twig-extension)
- [Symphony](https://symfony.com/doc/current/templates.html#register-an-extension-as-a-service)

### Craft CMS

For example, to register this extension in Craft CMS, just include the following code via a site module:

```php
use acalvino4\embed\Extension;

class Module extends BaseModule
{
  public function init(): void {
    //...
    Craft::$app->view->registerTwigExtension(new Extension());
  }
}
```
