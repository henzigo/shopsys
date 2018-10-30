# FAQ and Common Issues

This section provides only the basic answers to some of the most frequently asked questions.
For more detailed information about the Shopsys Framework, please see [Shopsys Framework Knowledge Base](../index.md).

## What are the phing targets?
Every phing target is a task that can be executed simply by php phing <target-name> command.
See more about phing targets in [Phing Targets](./phing-targets.md).

## What are the data fixtures good for?
Data fixtures are actually demo data that is already available at Shopys Frameworku.
Demo data rozdělujeme na singledomain a multidomain.
Instalace těchto demo dat probíha prostřednictvím phingového targetu `db-fixtures-demo-singledomain` pro základní demo data a prostřednictvím phingového targetu `db-fixtures-demo-multidomain` pro multidoménová data.
Kdy se tyto phingové targety spouštějí zjistíte ve vaši konfiguraci phingových targetu v souborech `build.xml` a `build-dev.xml`.
Demo data se využívají např při automatických testech nebo pro splnění potřeby nainstalování Shopsys Frameworku s nějakými základními daty.
Do not forget to extend the demo data when implementing some new features.

## How to perform a change of domain URL?
Akce změny url pozustava ze dvou kroku.
V prvním kroku upravíte url adresu domény v souboru `app/config/domains.yml` a v druhém kroku nahradíte všechny výskyty staré url adresy v databázi za novou adresu.
Tento scenář je podrobně popsán v návodu [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#4-change-the-url-address-for-an-existing-domain).

## How to use migrations and why the developers should use shopsys:migrations:generate instead of the default Doctrine one?
Migrace (nebo taky databázové migrace) jsou využívany k sjednocení databázového schématu s ORM.
Na Shopsys Frameworku se pro generování databázových migrací využívá phingový target `db-migrations-generate`.
Oproti standartnímu generování migrací v Doctrine aplikacích neprobíhá např generování "nevratných" migrací, tj migrace s operacemi typu `DROP` nebo `DELETE`.
Práce s migracemi je podrobněji popsána v [Database Migrations](./database-migrations.md)

## What are the diff versions of coding standards check commands and what are their limits?
Některé phingové targety pro kontrolu standardu mají kromě základní formy příkazu, která se využívá na kontrolu všech souboru, i formu příkazu se suffixem `-diff`, která se využívá je na kontrolu změněných souboru.
Např phingový target `standards` spustí kontrolu nad všemi soubory aplikace a phingový target `standards-diff` spustí kontrolu jen nad změněnými soubory aplikace.
Modifications are detected via git by comparison against the origin/master version.

## Is the application https ready or does it need some extra setting?
Shopys Framework je plně připraven pro HTTPS.

## What about translations and language constants export and import?
Definování uživatelských překladu popisku a hlášek probíha s využitím souboru `messages.cs.po` a `validators.cs.po`, where `cs` represents the version of translations for locale `cs`.
Tytou soubory jsou vygenerovány pro každý používaný locale a naleznete je ve složce `ShopBundle/Resources/translations/`.
Práce s jazyky je podrobněji popsána v návodu [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#3-locale-settings).

## What about deploy and production server setting?
Pro produkci doporučujeme instalaci pomocí Dockeru.
Postup instalace Shopsys Frameworku v produkci jako i postup deployování naleznete v návodu [Installation Using Docker on Production Server](../installation/installation-using-docker-on-production-server.md).

## How to set up the administration with a Czech locale?
Administration is by default in `en` locale.
If you want to switch it to another locale, override the method `getAdminLocale()` of the class `Shopsys\FrameworkBundle\Model\Localization\Localization`.
Tento scénář je popsán taky v návodu [How to Set Up Domains and Locales (Languages)](./how-to-set-up-domains-and-locales.md#36-locale-in-administration).

## What are the differences between "listable", "sellable", "offered" and "visible" products?
Produkty je možné rozdělit do několika skupin podle toho, jak se s těmito produkty pracuje nebo k čemu jsou používaný.

**Visible** - produkty, které jsou v databázi vedeny jako viditelné.
Podmínky, které musí produkt splňovat aby byl veden jako viditelný:
- nesmí být označen jako skrytý
- v případě, že má produkt nastaven selling start date, tak hodnota tohoto atributu musí být nastavena na datum v minulosti
- v případě, že má produkt nastaven selling end date, tak hodnota tohoto atributu musí být nastavena na datum v budoucnosti
- pro daný locale musí mít produkt vyplněn název
- pro danou doménu musí být produkt zařazen ve viditelném oddělení
- v případě, že se jedná o variantu, musí mít tato varianta vypočtenou cenu pro danou cenovou skupinu
- v případě, že se jedná o variantu, nesmí být skryta její hlavní varianta
- v případě, že se jedná o hlavní variantu, musí být viditelná alespoň jedná její varianta.

**Offered** - produkty, které splňují podmínky pro **visible** a současně jsou v databázi vedeny s příznakem `calculatedSellingDenied` = `FALSE`.
Příznak `calculatedSellingDenied` určuje, zdali není náhodou produkt vyprodán nebo v případě varianty, zdali není zakázaný prodej hlavní rodičovské položky, pod kterou táto varianta patří.

**Listable** - produkty, které splňuji podmínky pro **offered** a současně se nejedná o konkrétní varianty nějaké hlavní varianty.
Pro zobrazení ve výpisech produktu totiž nezobrazujeme konkrétní varianty položky ale zobrazujeme jen hlavní rodičovskou variantu dané konkrétní varianty.

**Sellable** - produkty, které splňuji podmínky pro **offered** a současně se nejedná o hlavní variantu (muže se tedy jednat o konkrétní variantu, nebo stadartní položku).
Sellable určuje položku, kterou je možné zakoupit.
Zakoupit lze jen standartní položky a konkrétní varianty nějaké hlavní rodičovské varianty.

## How calculated attributes work?
Na platformě Shopsys Framework existuje několik atributu, které se nanastavují přímo ale jejich hodnota je výsledkem nějakých automatických přepočtu atributu, např viditelnost oddělení, calculated visibility of product, etc.
Tato vypočtená hodnota je závislá na dalších parametrech daného objektu, např oddělení, které nemá pro locale konkrétní domény vyplněno název, bude na této doméně nastaveno jako neviditelné.
Samotné přepočty atributu mužou být inicializovány jako `immediate` nebo `scheduled`:

**immediate** - přepočet daného atributu je spuštěný při vyvolání události `kernel.response`.
See například třídu `Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator` and method `onKernelResponse`.

**scheduled** - přepočet atributu je spuštěn později.
Produkt nebo oddělení je jen označen k pozdejšímu přepočtu a samotný přepočet proběhne později - po spuštění přislušného cron modulu, see `Shopsys\FrameworkBundle\Command\RecalculationsCommand`.

Např v metodě `edit` v `Shopsys\FrameworkBundle\Model\Product\ProductFacade` se volá metoda `scheduleProductForImmediateRecalculation` třídy `Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler`.
Toto volání zabezpečí, že při volání metody `edit` je produkt označen k `immediate` přepočtu dostupnosti.
Tento přepočet je následně spuštěn po dokončení requestu a odchycení metody `onKernelResponse`.
Neplatí tedy fakt, že po provedení metody `edit` má již produkt dostupnost dopočtenou.
This approach with `kernel.response` event is a legacy feature and it will be removed in the future, see [Recalculating products availability and prices immediately instead of on finish request](https://github.com/shopsys/shopsys/issues/202).

## How do I change the environment?
The environment se řídí existenci souboru `PRODUCTION`, `DEVELOPMENT`, `TEST` v rootu vašeho projektu.
Tento soubor je vytvořen automaticky při spuštění příkazu `composer install`.
V případě příkazu `composer install` dojde k vytvoření souboru `DEVELOPMENT`.
V případě příkazu `composer install --no-dev` dojde k vytvoření souboru `PRODUCTION`.
Manuální změnu environmentu je možné provést pomocí příkazu `php bin/console shopsys:environment:change`.

## Is a cron configuration also part of the Shopsys Framework?
Yes, there is some prepared configuration for Shopsys Framework cron commands in a file `shopsys/packages/framework/src/Resources/config/services/cron.yml`.
Do not forget to set up a cron on your server for every 5 minutes.



