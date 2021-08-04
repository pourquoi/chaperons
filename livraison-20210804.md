mise à jour
------

git pull
cd chaperons-app
docker-compose build --force-rm --no-cache
docker-compose down
docker-compose up -d
cd ../chaperons-backend
docker-compose build --force-rm --no-cache
docker-compose down
docker-compose up -d


parsing des creches DSP+C
------
(vous pouvez le faire plus tard)
ajouter une colonne 'commercialisable' dans chaperons-backend/.docker/import/creches.csv et mettre 1 quand c'est une DSP+C
