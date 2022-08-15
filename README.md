#Wild-series
===========

- Cloner le repo
- Lancer un yarn install puis un composer install pour installer les dépendances.
- faire une copie du fichier .env en .env.local et modifier les informations d'accés à la base de données.
- lancer symfony console doctrine:database:create pour créer la base de données
- lancer les fixtures avec symfony console doctrine:fixtures:load
- symfony server:start
- yarn watch
