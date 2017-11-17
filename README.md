# PHP Scripts Bitbucket

Scripts to retrieve information from the Github repositories.




## Installation

Git clone the repository or download.

Run the composer install command:

```bash
$ composer install
```



## Usage

### Get a list of available commands
Get a list of available commands:

```bash
$ bin/github list
```

### Get a list of repositories
Get a list of repositories for the given team name:

```bash
$ bin/github repo:list --access_token='GithubPersonalAccessToken' digipolisgent
```


### Get a list of repositories filtered by type(s)
Get a list of repositories filtered by the provided type.

The supported types are:
* drupal_profile
* drupal_module
* drupal_theme
* php_package

```bash
$ bin/github repo:list --access_token='GithubPersonalAccessToken' --type=drupal_module digipolisgent`
```

You can filter for multiple types at once (OR):

```bash
$ bin/github repo:list --access_token='GithubPersonalAccessToken' --type=drupal_module --type=drupal_theme digipolisgent
```


### Get a list of repositories filtered by pattern
Get a list of repositories filtered by a regular expression. The pattern will
be applied to the repository name.

```bash
$ bin/github repo:list --access_token='GithubPersonalAccessToken' --pattern="/^drupal\_/" digipolisgent
```

You can filer by multiple patterns at once (OR):

```bash
$ bin/github repo:list --access_token='GithubPersonalAccessToken' --pattern="/^drupal\_/" --pattern="/^php\_/" digipolisgent
```
