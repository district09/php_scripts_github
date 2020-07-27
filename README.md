# PHP Scripts GitHub

Scripts to retrieve information from the Github repositories.

[![Maintainability](https://api.codeclimate.com/v1/badges/0c4bdd5079dfb66f3803/maintainability)](https://codeclimate.com/github/digipolisgent/php_scripts_github/maintainability)



## Requirements

* PHP 7.1 or newer
* Composer



## Installation

Git clone the repository or download.

Run the composer install command:

```bash
composer install
```



## Usage

### Get a list of available commands

Get a list of available commands:

```bash
bin/github list
```

### Get a list of repositories

Get a list of repositories for the given team name:

```bash
bin/github repo:list --access-token='GithubPersonalAccessToken' digipolisgent
```

### Get a list of repositories filtered by type(s)

Get a list of repositories filtered by the provided type.

The supported types are:
* drupal_profile
* drupal_module
* drupal_theme
* php_package

```bash
bin/github repo:list --access-token='GithubPersonalAccessToken' --type=drupal_module digipolisgent`
```

You can filter for multiple types at once (OR):

```bash
bin/github repo:list --access-token='GithubPersonalAccessToken' --type=drupal_module --type=drupal_theme digipolisgent
```

### Get a list of repositories filtered by pattern

Get a list of repositories filtered by a regular expression. The pattern will
be applied to the repository name.

```bash
bin/github repo:list --access-token='GithubPersonalAccessToken' --pattern="/^drupal\_/" digipolisgent
```

You can filer by multiple patterns at once (OR):

```bash
bin/github repo:list --access-token='GithubPersonalAccessToken' --pattern="/^drupal\_/" --pattern="/^php\_/" digipolisgent
```

### Find usages of a project within MakeFile sites

List all drupal sites where a project (install profile, module or theme) is
used.

```bash
bin/github makefile:usage --access-token='GithubPersonalAccessToken' digipolisgent project_name
```

You can search usages for multiple projects at once:

```bash
bin/github makefile:usage --access-token='GithubPersonalAccessToken' digipolisgent project_name1 project_name2 project_name3
```

### Find usages of a project within Composer sites

List all drupal sites where a project (install profile, module or theme) is
used.

```bash
bin/github composer:usage --access-token='GithubPersonalAccessToken' digipolisgent project_name
```

You can search usages for multiple projects at once:

```bash
bin/github composer:usage --access-token='GithubPersonalAccessToken' digipolisgent project_name1 project_name2 project_name3
```
