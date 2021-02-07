prerequis: git, docker, docker-compose

```git clone git@github.com:pourquoi/chaperons.git```
```cd chaperons```

## install backend
```shell
cd chaperons-backend
docker-compose up -d
docker exec -it cartochaperons_api bin/up.sh
docker exec -it cartochaperons_api bin/console app:create-user commercial
docker exec -it cartochaperons_api bin/console app:import-nurseries var/import/creches.csv
```

et noter le login/mdp pour le transmettre aux utilisateurs

## install app

```shell
cd chaperons-app
cp src/environments/environment.ts.dist src/environments/environment.ts
```

et éditer API_HOST dans src/environments/environment.ts (mettre l'adresse IP ou DNS)

```
docker-compose up -d
```

## alimentation des creches

mettre un fichier creches.csv dans ```chaperons-backend/.docker/import/creches.csv```

```docker exec -it cartochaperons_api bin/console app:import-nurseries var/import/creches.csv```

pour la mise à jour régulière on peut par exemple ftp le fichier dans chaperons-backend/.docker/import/creches.csv, il sera automatiquement parsé dans la journée.

pour le format voir chaperons-backend/var/creches.csv