API Statistiques — Gestion de groupes (PHP natif)

- Lancer l'API (serveur de dev intégré à PHP)

php -S localhost:8000 index.php


- Endpoints


GET  /stats/profils  Répartition des individus par profil 
GET  /stats/individus-par-groupe  Nombre d'individus par groupe 


curl http://localhost:8000/stats/profils
curl http://localhost:8000/stats/individus-par-groupe


