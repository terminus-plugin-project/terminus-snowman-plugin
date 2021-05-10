# Terminus Site Defrost Plugin

[![Terminus v2.x Compatible](https://img.shields.io/badge/terminus-v2.x-green.svg)](https://github.com/morganestes/terminus-defrost-plugin)

Terminus plugin to defrost a [frozen Pantheon website](https://pantheon.io/docs/platform-considerations#inactive-site-freezing).

Do you want to build a snowman? If so, whether you do or don't, having a frozen Pantheon site
is probably not how you want to build that snowman. This plugin allows you to unfreeze those
Pantheon sites that have been frozen.

Based on the Snowman plugin at https://github.com/terminus-plugin-project/terminus-snowman-plugin.

## Examples (with aliases)
### Site name
```
$ terminus site:defrost my-cool-site
```

### URL
```
$ terminus site:thaw https://dev-my-cool-site.pantheonsite.io/
```

### ID/UUID
```
$ terminus site:unfreeze de305d54-75b4-431b-adb2-eb6b9e546014
```

## Installation
For help installing, see [Manage Plugins](https://pantheon.io/docs/terminus/plugins/)
```
mkdir -p ~/.terminus/plugins
composer create-project -d ~/.terminus/plugins morganestes/terminus-snowman-plugin:~1
```

## Help
Run `terminus list site` for a complete list of available commands. Use `terminus help <command>` to get help on one command.
