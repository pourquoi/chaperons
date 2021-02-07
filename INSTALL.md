requis docker, docker-compose

*backend*
```shell
cd chaperons-backend
docker-compose up -d
docker exec -it cartochaperons_api bin/up.sh
```

*app*

```shell
cd chaperons-app
cp src/environments/environment.ts.dist src/environments/environment.ts
```

et éditer API_HOST dans src/environments/environment.ts (adress IP ou DNS)

```
docker-compose up -d
```