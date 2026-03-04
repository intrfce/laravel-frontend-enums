# Upgrade Guide

## 0.7.x -> 1.x

### Add the attribute.

1.x only supports publishing enums that are marked by the `#[PublishEnum]` attribute - go through the list of files you had previously listed in the `PublishEnums::publish([...])` method, and give each class the attribute. Or have AI do it for you!

Delete the `PublishEnums::publish()` call in your application service provider or elsewhere.

### Configure Directories

Publish the config file (see the readme), and configure the directories your enums live in, by default, we scan the entire `./app` folder.

### Configure Typescript

If you were using the publish as typescript feature, ensure you set `as_typescript = true` in the config file to do it globally, or add the `addTypescript:true` argument to the attribute above your class.

That should be it!
