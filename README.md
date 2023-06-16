> **Note**
> Use this library if you are a professional developer
> https://github.com/Cosmoverse/BlockData

# BlockData
A virion for PocketMine-MP that lets plugins store arbitrary block data

Integrate the virion itself into your plugin or you could also use it as a composer library by running the command below:

`composer require nhanaz/blockdata`

## API documentation
There's no documentation yet, but you can check out the [demo plugin](https://github.com/NhanAZ/BlockData_Example_Plugin) or [Dependency graph](https://github.com/NhanAZ/BlockData/network/dependents) which shows how to use its API in a plugin.

## Including in other plugins
This library supports being included as a [virion](https://github.com/poggit/support/blob/master/virion.md).

If you use [Poggit](https://poggit.pmmp.io) to build your plugin, you can add it to your `.poggit.yml` like so:

```yml
--- # Poggit-CI Manifest. Open the CI at https://poggit.pmmp.io/ci/YourGithubUserName/YourPluginName
build-by-default: true
branches:
- master
projects:
  YourPluginName:
    path: ""
    libs:
      - src: NhanAZ/BlockData/BlockData
        version: x.y.z
...

```

# Contact
[![Discord](https://img.shields.io/discord/986553214889517088?label=discord&color=7289DA&logo=discord)](https://discord.gg/j2X83ujT6c)\
**You can contact me directly via Discord `NhanAZ#9115`**
