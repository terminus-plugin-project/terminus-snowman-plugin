# Terminus Snowman Plugin

[![Terminus v2.x Compatible](https://img.shields.io/badge/terminus-v2.x-green.svg)](https://github.com/terminus-plugin-project/terminus-snowman-plugin)

Terminus plugin to unfreeze a website.

Do you want to build a snowman? If so, whether you do or don't, having a frozen Pantheon site
is probably not how you want to build that snowman. This plugin allows you to unfreeze those
Pantheon sites that have been frozen.

Based on the original Snoman plugin at https://github.com/terminus-plugin-project/terminus-snowman-plugin.

## Examples
### Unfreeze a site
```
$ terminus site:unfreeze <site>
```

### Also unfreeze a site
```
$ terminus snowman <site>
```

## Installation
For help installing, see [Manage Plugins](https://pantheon.io/docs/terminus/plugins/)
```
mkdir -p ~/.terminus/plugins
composer create-project -d ~/.terminus/plugins morganestes/terminus-snowman-plugin:~1
```

## Help
Run `terminus list site:unfreeze` for a complete list of available commands. Use `terminus help <command>` to get help on one command.
