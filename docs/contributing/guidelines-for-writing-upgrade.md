# Guidelines for writing UPGRADE.md

Keep in mind that upgrade instructions are written for users who do not understand our system well, so more clear they are, the more helpful they are.

## Introduction

- Our users work in a clone of project-base, and even when they do the upgrade, their project-base still needs to be upgraded.
  Every time you change/add anything in the project-base, write upgrade instruction how to repeat this work.
    - for anything with docker, phing, frontend, config, etc.
- Make instructions as easy to follow as possible
    - Good example: [postgres upgrade](https://github.com/shopsys/shopsys/blob/master/UPGRADE.md#postgresql-upgrade)
    - Copyable commands are great
    - Bad example: _"Apply changes done in PR..."_, however link to particular diff is all right
- If you mention a file, make a link for it
    - This is especially important for files in project-base, as users don't have new changes in their project-base
- Link files in an accurate version, because the project evolves in time
    - Good example: [installation using docker - version alpha5](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
    - Bad example: [installation using docker - master](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker-application-setup.md)
- Write instructions
    - Good example: _"Do this, then that"_
    - Bad example: _"This was done, this was changed"_

## Files related to upgrade

The main file where a project developer should start looking for instructions is [`UPGRADE.md`](https://github.com/shopsys/shopsys/blob/master/UPGRADE.md) file in the monorepo root.

This file contains information for the contributors in the form of the link to [`upgrade/upgrading-monorepo.md`](https://github.com/shopsys/shopsys/blob/master/upgrade/upgrading-monorepo.md) file.

Instructions for developers building a project based on project-base should follow.
First, there must be general information about upgrading with recommended steps and a typical upgrade sequence,
followed by a list of links to upgrade guides for each version.
These versions should be placed in a [`upgrade/`](https://github.com/shopsys/shopsys/tree/master/upgrade/) folder.

## Structure of upgrade files

Each upgrade file must have a link to the main UPGRADE.md file with general information about the upgrade and may contain one or more of the following main sections:

- shopsys/framework
- shopsys/coding-standards
- shopsys/form-types-bundle
- shopsys/http-smoke-testing
- shopsys/migrations
- shopsys/monorepo-tools
- shopsys/plugin-interface
- shopsys/brand-feed-luigis-box
- shopsys/category-feed-luigis-box
- shopsys/product-feed-google
- shopsys/product-feed-mergado
- shopsys/product-feed-heureka
- shopsys/product-feed-heureka-delivery
- shopsys/product-feed-zbozi
- shopsys/product-feed-luigis-box
- shopsys/article-feed-luigis-box
- shopsys/luigis-box
- shopsys/administration
- shopsys/convertim

Each section must contain instructions relevant only to the package they cover, and the sections have to be ordered as they are in the list above.

Each step should have a link to the related pull request and may contain additional links to make instruction clearer.

### Section shopsys/framework

Because this section is expected to be the longest, it should contain a finer division into one or more of the following sub-sections:

- Infrastructure
    - related to Docker, Kubernetes, Environment settings, etc.
    - instruction to rebuild images must occur only once
- Composer dependencies
    - related to composer.json
- Configuration
    - related with parameters, YML configuration files, ...
- Tools
    - Phing, PHPStan, PHPUnit, etc.
- Database migrations
    - Which database changes were introduced in the current version
- Security
    - Important security upgrades
- Application
    - Changes in a code that may require some changes in a particular implementation.
