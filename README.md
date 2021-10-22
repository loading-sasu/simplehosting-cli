# Simple Hosting CLI

Simple tool for [Gandi simple hosting](https://www.gandi.net/en/simple-hosting)

It allows to deploy codebase and synchronize datas from and to remove websites



## Installation

### From build

Download binary and store it somewhere in your `$PATH`

```bash
wget -O /usr/local/sbin/simple https://raw.githubusercontent.com/loading-sasu/simplehosting-cli/master/builds/simple
chmod a+x /usr/local/sbin/simple
```

### From source

Clone this repository, install dependencies then install binary system-wide :

```bash
git clone git@github.com:loading-sasu/simplehosting-cli.git
cd simplehosting-cli
composer install
./simple app:build
```

Move the freshly built `simple` cli somewhere in your `$PATH`

```bash
mv builds/simple /usr/local/sbin/simple
```



## Usage

### Introduction

[Gandi Simple Hosting](https://www.gandi.net/en/simple-hosting) is cheap and useful, but sometimes difficult to work with. There is no SSH connections but [an emergency one](https://docs.gandi.net/en/simple_hosting/connection/ssh.html) and every deployment should use [git](https://docs.gandi.net/en/simple_hosting/connection/git.html) or [SFTP](https://docs.gandi.net/en/simple_hosting/connection/sftp.html). Even git deployment is sometimes painful because you cannot deploy any git repository but the one provided with the website setup on the Simple Hosting instance.

This simple tool aims to simplify a little the development process for everyone using a remote Simple Hosting.

Let say you own iamonline.com and you did install a [Grav CMS](https://getgrav.org/) from the available [automatic installs](https://docs.gandi.net/en/simple_hosting/one_click/index.html). And of course, you host is on a Simple Hosting instance.
This instance ID is `123456` and is managed on the `sd0` cluster of Gandi and your administration panel should be reachable at https://123456.admin.sd0.gpaas.net/

You may have downloaded your grav website, and setup a local git repository (`~/Developer/iamonline`, maybe). 
You have defined your git remote as written in your website configuration panel, such as
```bash
git remote add gandi git+ssh://123456@git.sd0.gpaas.net/www.iamonline.com.git
```

Maybe you also have setup a staging website in order to verify your changes before push them live. Let say you are using the same local repository with a different remote :
```bash
git remote add staging git+ssh://123456@git.sd0.gpaas.net/stage.iamonline.com.git
```

Perfect, you're ready to use `simple` cli

### Deploy

If you want to deploy your pushed modifications, you just have to call `simple` with the `deploy` argument :

```bash
❯ simple deploy

 Which environment do you want to deploy: [www.iamonline.com]:
  [gandi  ] www.iamonline.com
  [staging] stage.iamonline.com
 >
```

You now have to type in the remote name (auto-complete will help you). If you don't type anything, the default one will be deployed (the first remote found in git config)

```bash
❯ simple deploy

 Which environment do you want to deploy: [www.iamonline.com]:
  [gandi  ] www.iamonline.com
  [staging] stage.iamonline.com
 > gandi
 
 Deploying code: ✔
```

Under the hood, the `simple` cli used the git remote url (`git+ssh://123456@git.sd0.gpaas.net/www.iamonline.com.git`) to build the deployment command (`ssh 123456@git.sd0.gpaas.net deploy www.iamonline.com.git`) and execute it.

### Sync

Your Grav website stores your webpage inside `user/pages/` folder. You may have gitignore them, in order to avoid publishing your local tests online. They are not versioned. But sometimes it would be useful to get the latest version of the website. The official way is to execute sftp commands from the last century and get files from remote path to local one. `simple` cli makes this operation simple. 

If you want to get the pages files and folder from remote, you have to sync my local path `htdocs/user/pages` (Simple Hosting uses htdocs as document root for reasons and there is no way to bypass it) and request `simple` to sync upward or downward, recursivley or not. In this case, you may want to download all pages recursively

```bash
❯ simple sync htdocs/user/pages -r

 Which environment do you want to sync: [www.iamonline.com]:
  [gandi  ] www.iamonline.com
  [staging] stage.iamonline.com
 >
 
 Downloading htdocs/user/pages: ✔
```

And done. If you want to sync back you local pages to the remote website, you can use the `direction` argument

```bash
 ❯ simple sync --recursive htdocs/user/pages up
```

Of course, this works with every (versioned or not) files or folder. Your prestashop images folder, your whole Wordpress website, etc.

### Help

For a complete `simple` usage, request `help` argument

```bash
❯ simple help
Description:
  Display help for a command

Usage:
  help [options] [--] [<command_name>]

Arguments:
  command_name          The command name [default: "help"]

Options:
      --format=FORMAT   The output format (txt, xml, json, or md) [default: "txt"]
      --raw             To output raw command help
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  The help command displays help for a given command:

    /usr/local/sbin/simple help list

  You can also output the help in other formats by using the --format option:

    /usr/local/sbin/simple help --format=xml list

  To display the list of available commands, please use the list command.

```

```bash
❯ simple help sync
Description:
  Sync data from or to simple hosting

Usage:
  sync [options] [--] <path> [<direction>]

Arguments:
  path                  The relative path to sync (required)
  direction             Sync direction. Up or down (required) [default: "down"]

Options:
  -r, --recursive       Specify if the sync should be recursive or not (optional)
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```

