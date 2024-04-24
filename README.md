
```diff
- Warning: README is not up to date yet.
```

# Laravel dynamic forms

Laravel dynamic forms is a package used to generate forms based on model input. 
It is just a trait which adds some features to models for extracting data from the database.

## Installation

(Recommended): publish config for editing (necessary for custom datatypes)
```bash
php artisan vendor:publish --tag=dynamic-forms/config
```
(Optional): publish view for editing
```bash
php artisan vendor:publish --tag=dynamic-forms/view
```
(Optional): publish all files
```bash
php artisan vendor:publish --tag=dynamic-forms/all
```

## Usage

### Add form to views
Use the following command within the bladetemplate where you want to display the form
```php
@include("dynamicforms::form")
```

### Or pass it through a variable
```php
$form = view("dynamicform::forms", $form)->render();

compact('form');
```

## License
[MIT](https://choosealicense.com/licenses/mit/)
