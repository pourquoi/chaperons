prerequis: docker, docker-compose

*backend*
```shell
cd chaperons-backend
docker-compose up -d
docker exec -it cartochaperons_api bin/up.sh
docker exec -it cartochaperons_api bin/console app:create-user admin
```

et noter le login/mdp pour le transmettre aux utilisateurs

*app*

```shell
cd chaperons-app
cp src/environments/environment.ts.dist src/environments/environment.prod.ts
```

et éditer API_HOST dans src/environments/environment.ts (adress IP ou DNS)

```
docker-compose up -d
```