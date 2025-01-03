# Grid Rendering Customization

Default [Twig](https://twig.symfony.com/) template for rendering each grid can be found in [`src/Resources/views/Admin/Grid/Grid.html.twig`](https://github.com/shopsys/shopsys/blob/master/packages/framework/src/Resources/views/Admin/Grid/Grid.html.twig).
The template is composed of a set of Twig blocks, and you can override any of them when there is a need for customization of the default appearance.

To customize your grid, you need to create a new template extending the original one, override appropriate blocks, and then set the template as a theme of your grid using `Grid::setTheme` method.

## Blocks that are being overridden at most

- `grid_title_cell_id_<column_id>`
    - `<column_id>` stands for the ID of the column that is defined during the grid creation by the first argument of `Grid::addColumn` method
    - handy when you need to override a column title in a grid defined in the framework without overriding the grid factory
- `grid_value_cell_id_<column_id>`
    - `<column_id>` stands for the ID of the column that is defined during the grid creation by the first argument of `Grid::addColumn` method
    - used when you need to change the appearance of values in a particular column
    - the original value is available as `value` variable
- `grid_no_data`
    - the block contains a message that is displayed when there are no data in the grid
    - the default (translatable) value is "No records found"

## Rendering type Money in administration

When creating a grid containing prices in administration, you can provide ID of the domain as `domainId` in the datasource.
Money will format to the given domain default currency, and if `domainId` is unavailable, it will fall back to default administration currency.

## Example

Let's say we have a grid of salesmen (in fact, such a grid is created in ["Create basic grid"](../cookbook/create-basic-grid.md) cookbook)
and we want to display all their names in bold, and also, we want to be more specific when there are no salesmen in our database.

1.  Create a new template that extends to the original one and override the blocks you need:

    <!-- language: lang-twig -->

        {# templates/Admin/Content/Salesman/listGrid.html.twig #}
        {% extends '@ShopsysFramework/Admin/Grid/Grid.html.twig' %}

        {% block grid_no_data %}
            {{ 'There are no salesmen in your database.' }}
        {% endblock %}

        {% block grid_value_cell_id_name %}
            <strong>{{ value }}</strong>
        {% endblock %}

2.  Set the new theme for your grid:

    <!-- language: lang-php -->

        $grid->setTheme('Admin/Content/Salesman/listGrid.html.twig');
