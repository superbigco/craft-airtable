# Airtable plugin for Craft CMS 3.x

Sweet saving and fetching of data with [Airtable](https://airtable.com/invite/r/znUACjay)

![Screenshot](resources/icon.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require superbig/craft-airtable

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Airtable.

## Airtable Overview

[Airtable](https://airtable.com/invite/r/znUACjay) is a human-friendly database solution that makes it super easy to manage both simple and complex, relational data.

## Configuring Airtable

```php
<?php
return [
    // Find this on https://airtable.com/account
    'apiKey'        => '',

    // Find this on https://airtable.com/api
    'base'
                    => '',
    // The name of the default table. Make sure the capitalization is correct
    'defaultTable'         => '',

    // Allowed field keys per table. This matches the field names in the table
    'allowedFields' => [ ],
];
```

## Using Airtable

To get records from a table:

```twig
{% set records = craft.airtable.records.table('contacts').find() %}
```

To change base
```twig
{% set records = craft.airtable.records.base('appvv0LuV2oP5AsS').table('Equipment').find() %}
```

_Example form_

```twig
<form method="post" accept-charset="UTF-8">
    {{ csrfInput() }}
    {{ redirectInput('airtable?success={Name}') }}
    <input type="hidden" name="action" value="airtable/save">
    <input type="hidden" name="table" value="{{ 'Content'|hash }}">
    <input type="hidden" name="enabled" value="1">

    {% macro errorList(errors) %}
        {% if errors %}
            <ul class="errors">
                {% for error in errors %}
                    <li>{{ error }}</li>
                {% endfor %}
            </ul>
        {% endif %}
    {% endmacro %}

    {% from _self import errorList %}

    {# Display server error #}
    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('server')) }}
    {% endif %}

    <label for="name">Name</label>
    <input id="name" type="text" name="Name"
            {%- if airtable is defined %} value="{{ airtable.Name }}"{% endif -%}>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Name')) }}
    {% endif %}

    <label for="email">E-mail</label>
    <input id="email" type="text" name="E-mail"
            {%- if airtable is defined %} value="{{ airtable['E-mail'] }}"{% endif -%}>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('E-mail')) }}
    {% endif %}

    <label for="notes">Notes</label>
    <textarea id="notes" name="Notes">
        {%- if airtable is defined %}{{ airtable.Notes }}{% endif -%}
    </textarea>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Notes')) }}
    {% endif %}

    <fieldset>
        {% set options = ['First option', 'Second option'] %}
        {% for option in options %}
            <label>
                <input type="radio" name="Single-select" value="{{ option }}"
                        {%- if airtable is defined and airtable['Single-select'] == option %} checked{% endif -%}>
                {{ option }}
            </label>
        {% endfor %}
    </fieldset>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Single-sele{ct')) }}
    {% endif %}

    <fieldset>
        {% set options = ['First option', 'Second option'] %}
        {% for option in options %}
            <label>
                <input type="checkbox" name="Multi-select[]" value="{{ option }}"
                        {%- if airtable is defined and option in airtable['Multi-select'] %} checked{% endif -%}>
                {{ option }}
            </label>
        {% endfor %}
    </fieldset>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Multi-select')) }}
    {% endif %}

    <fieldset>
        <label for="date">Date</label>
        <input id="date" type="date" name="Date"
                {%- if airtable is defined %} value="{{ airtable['Date'] }}"{% endif -%}>
    </fieldset>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Date')) }}
    {% endif %}

    <label>
        <input type="checkbox" name="Checkbox" value="1"
                {%- if airtable is defined and airtable['Checkbox'] %} checked{% endif -%}>
        Accept terms
    </label>

    {% if airtable is defined %}
        {{ errorList(airtable.getErrors('Checkbox')) }}
    {% endif %}

    <input type="submit" value="Save">
</form>
```

_Find records in table_
```twig
{% set records = craft.airtable.records.table('contacts').find() %}
{% if records | length %}
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>E-mail</th>
            <th>Date</th>
        </tr>
        </thead>
    {% for record in records %}
        {% set fields = record.getFields() %}
        <tr>
            <td>{{ fields['Name']|default('') }}</td>
            <td>{{ fields['E-mail']|default('') }}</td>
            <td>{{ fields['Date']|default('') }}</td>
        </tr>
    {% endfor %}
    </table>
{% endif %}
```

_Find records in table with criteria_

```twig
{% set records = craft.airtable.records.table('contacts').find({
    'Name': 'Thomas'
}) %}
{% if records | length %}
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>E-mail</th>
            <th>Date</th>
        </tr>
        </thead>
        {% for record in records %}
            {% set fields = record.getFields() %}
            <tr>
                <td>{{ fields['Name']|default('') }}</td>
                <td>{{ fields['E-mail']|default('') }}</td>
                <td>{{ fields['Date']|default('') }}</td>
            </tr>
        {% endfor %}
    </table>
{% endif %}
```

## Airtable Roadmap

Some things to do, and ideas for potential features:

* Support attachments
* Add logging

Brought to you by [Superbig](https://superbig.co)
