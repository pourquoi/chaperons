carto chaperons api
=================

[Installation] (/INSTALL.md)

Mettre à jour les crèches:

```shell
bin/console app:import-nurseries /path/to/file.csv
```

Ajouter un utilisateur:

```shell
bin/console app:create-user roger
```

* [rest bundle] (http://symfony.com/doc/master/bundles/FOSRestBundle/index.html)
* [phantomjs] (http://phantomjs.org/)


le SSO peut s'implementer au niveau du login dans UserController::postUserLoginAction