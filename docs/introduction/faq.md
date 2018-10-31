# FAQ and Common Issues

This section provides only the basic answers to some of the most frequently asked questions.
For more detailed information about the Shopsys Framework, please see [Shopsys Framework Knowledge Base](../index.md).

## What are the phing targets?
Every phing target is a task that can be executed simply by php phing <target-name> command.
See more about phing targets in [Phing Targets](./phing-targets.md).

## What are the data fixtures good for?
Data fixtures are actually demo data that are already available in the Shopys Framework.
There are two kinds of demo data, the singledomain and the multidomain.
For the installation of the basic demo data, use the phing target `db-fixtures-demo-singledomain`.
For the installation of the multidomain demo data, use the phing target `db-fixtures-demo-multidomain`.
These phing targets are also triggered as the part of others phing targets, see `build.xml` and `build-dev.xml`.
Demo data are used, for example, during the automatic tests or if you want to install Shopys Framework with some basic data.
Do not forget to extend the demo data when implementing some new features.

## How to perform a change of domain URL?
The change of domain url requires two steps.
In the first step, you need to modify the domain url in the configuration file `app/config/domains.yml`.
In the second step, you need to replace all occurrences of the old url address in the database with the new url address.
This scenario is described in more detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#4-change-the-url-address-for-an-existing-domain).

## How to use migrations and why the developers should use shopsys:migrations:generate instead of the default Doctrine one?
Migrations (or also sometimes calld as database migrations) are used to unify the database schema with ORM.
On Shopsys Framework, you can use the phing target `db-migrations-generate` for migrations generation.
Compared to the standard migrations generation process from Doctrine, this phing target does not generate "irreversible" migrations, such as migrations with the operations `DROP` or `DELETE`.
Migrations are described more in detail in the docs [Database Migrations](./database-migrations.md)

## What are the diff versions of coding standards check commands and what are their limits?
Some of the coding standards check commands are available in two forms.
The first basic form is used to check all files.
The second additional form, commands with the suffix `-diff`, is used to check only modified files.
For example, the phing target `standards` starts checking of all files in the application while the phing target `standards-diff` starts checking only the modified files.
Modifications are detected via git by comparison against the origin/master version.

## Is the application https ready or does it need some extra setting?
Shopys Framework is fully prepared for HTTPS.

## What about translations and language constants export and import?
To set up the user translations of labels and messages, use the files `messages.cs.po` and `validators.cs.po`, where `cs` represents the version of translations for a locale `cs`.
These files are generated for each locale you use, and you can find them in a directory `ShopBundle/Resources/translations/`.
Changes and language settings are described more in detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#3-locale-settings).

## What about deploy and production server setting?
We recommend installation using the Docker for production.
See how to install Shopsys Framework in production and how to proceed when deploying in the tutorial [Installation Using Docker on Production Server](../installation/installation-using-docker-on-production-server.md).

## How to set up the administration with a Czech locale?
The administration is by default in `en` locale.
If you want to switch it to the another locale, override the method `getAdminLocale()` of the class `Shopsys\FrameworkBundle\Model\Localization\Localization`.
This scenario is described in more detail in the tutorial [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#36-locale-in-administration).

## What are the differences between "listable", "sellable", "offered" and "visible" products?
Products can be grouped into several groups according to their current status or according to what they are used for.

**Visible** - products that appear in the database as visible. 
The conditions which the product must satisfy to appear as visible:
- the product must not be set as hidden
- if the attribute "selling start date" is filled in, the value of this attribute must be set to the date in the past
- if the attribute "selling end date" is filled in, the value of this attribute must be set to the date in the future
- the product must have a name for the specific locale
- if the product is a variant, there must exist calculated price for this variant for the specific pricing group
- if the product is a variant, her main variant must not be set as hidden
- if the product is the main variant, at least one of her variants must be visible

**Offered** - products that satisfied the conditions for **visible** and at the same time they appear in the database with the attribute `calculatedSellingDenied` = `FALSE`.
The `calculatedSellingDenied` attribute shows whether the product is already sold out or if the product is a variant with the main variant that is not set up with selling denied.

**Listable** - products that satisfied the conditions for **offered** and at the same time, these products are not the variants.
By default, the specific variants are not included in the product lists.
Only the main variants are included in the product lists.

**Sellable** - products that satisfied the conditions for **offered** and at the same time, these products are not the main variants (in other words, these products are either the specific variants or they are the standard products).
Sellable products are products that can actually be purchased.
Only the standard products or the specific variants can actually be purchased.

## How calculated attributes work?
Some attributes that are used on the Shopsys Framework are not set directly, but their value stands out as a result of the values of other attributes.
The values of these special attributes are calculated automatically.
For example, if a category of products does not have a name for a locale of the specific domain, this category will be automatically set as invisible on this domain.
The recalculations of these special attributes can be initialized as `immediate` or `scheduled`:

**immediate** - recalculation is initialized when the event `kernel.response` is caught.
See a class `Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator` and a method `onKernelResponse`.

**scheduled** - recalculation is initialized later.
A product or a category of products can be marked for scheduled recalculation, the recalculation itself is initialized with a cron module, see a class `Shopsys\FrameworkBundle\Command\RecalculationsCommand`.

For example, a method `edit` of a class `Shopsys\FrameworkBundle\Model\Product\ProductFacade` calls a method `scheduleProductForImmediateRecalculation` of a class `Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler`.
Through this request, the product is marked for `immediate` recalculation of availability.
The recalculation itself is initialized when the event `kernel.response` is caught by a method `onKernelResponse`.
It can be seen from this description that the product does not have recalculated availability immediately after the method `edit` is completed.
This approach with `kernel.response` event is a legacy feature and it will be removed in the future, see [Recalculating products availability and prices immediately instead of on finish request](https://github.com/shopsys/shopsys/issues/202).

## How do I change the environment?
The environment is determined by the existence of the files `PRODUCTION`, `DEVELOPMENT`, `TEST` in the root of your project.
This file is created automatically during the run of a command `composer install`.
If the command `composer install` is executed, the file `DEVELOPMENT` is created.
If the command `composer install --no-dev` is executed, the file `PRODUCTION` is created.
You can change the environment manually by using the command `php bin/console shopsys:environment:change`.

## Is a cron configuration also part of the Shopsys Framework?
Yes, there is some prepared configuration for Shopsys Framework cron commands in a file `shopsys/packages/framework/src/Resources/config/services/cron.yml`.
Do not forget to set up a cron on your server for every 5 minutes.



