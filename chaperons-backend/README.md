carto chaperons api
=================

[Installation] (/INSTALL.md)

Mise à jour des crèches:
mettre un fichier creches.csv dans ```.docker/import/creches.csv```, il est automatiquement parsé chaque jour s'il existe. L'import n'est pas destructif.
Pour le format voir ```var/creches.csv```.

Mettre à jour les crèches manuellement:

```shell
docker exec -it cartochaperons_api bin/console app:import-nurseries /path/to/file.csv
```

Ajouter un utilisateur:

```shell
docker exec -it cartochaperons_api bin/console app:create-user roger
```

* [rest bundle] (http://symfony.com/doc/master/bundles/FOSRestBundle/index.html)
* [phantomjs] (http://phantomjs.org/)


le SSO peut s'implementer au niveau du login dans UserController::postUserLoginAction