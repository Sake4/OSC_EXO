# Gestion de groupes

Application de gestion de groupes de projet, organisée en hiérarchie
**Encadrant → Projet → Groupe → Individu**. Chaque livrable (API, export, vues) est réalisé dans une technologie différente.

## Base de données

Base **PostgreSQL** hébergée sur Render, composée de 4 tables liées par
clés étrangères (`encadrant`, `projet`, `groupe`, `individu`) ainsi qu'une table `log`. Un trigger PostgreSQL journalise automatiquement chaque insertion, modification ou suppression dans la table `log`.

## API Create/Delete — Python (Flask)

https://api-flask-aqff.onrender.com
API REST qui permet d'ajouter et de supprimer des enregistrements dans les tables. Elle gère les contraintes d'intégrité référentielle (rejet propre si une clé étrangère est invalide) et la suppression en cascade des individus liés à un groupe supprimé.

| Méthode | Route | Description |
| POST | /groupes | Créer un groupe |
| DELETE | /groupes/:id_groupe | Supprimer un groupe |
| POST | /individus | Créer un individu |
| DELETE | /individus/:id_individu | Supprimer un individu |

## API Read/Update — Node.js (Express)

https://api-node-prqv.onrender.com
API REST qui permet de lire et de modifier les enregistrements des tables. Les requêtes SQL sont écrites directement (sans ORM) avec des paramètres bindés pour éviter les injections SQL.

| Méthode | Route | Description |
| GET | /groupes | Lister tous les groupes |
| GET | /groupes/:id | Détail d'un groupe |
| PUT | /groupes/:id | Modifier un groupe |
| GET | /individus | Lister tous les individus |
| GET | /individus/:id | Détail d'un individu |
| PUT | /individus/:id | Modifier un individu |

## API Statistiques — PHP natif

https://osc-exo.onrender.com
API REST qui retourne deux statistiques calculées à partir des données :
la répartition des individus par profil, et le nombre d'individus par
groupe. Les requêtes SQL utilisent `GROUP BY` et un `LEFT JOIN` pour inclure les groupes vides dans le second résultat.

| Méthode | Route | Description |
| GET | /stats/profils | Répartition des individus par profil |
| GET | /stats/individus-par-groupe | Nombre d'individus par groupe |

## Export XML — Python (script indépendant)

Script qui exporte l'intégralité des données de la base en un fichier XML unique, en respectant la hiérarchie complète des relations. Les données sont regroupées par clé étrangère avant construction de l'arbre XML, pour éviter les requêtes répétées.

## Vue web — Lecture & suppression (HTML/JS natif)

Page web statique qui affiche les groupes et individus en consommant l'API Read/Update, et permet leur suppression via l'API Create/Delete. Chaque suppression déclenche un rechargement des données affichées et une confirmation visuelle (toast).

## Déploiement sur Render

Chaque API est déployée comme un **Web Service** Render distinct, connect au même dépôt GitHub via un "Root Directory" pointant vers son sous-dossier. La base de données PostgreSQL est également hébergée sur Render, et son URL de connexion est transmise à chaque service via la variable d'environnement `DATABASE_URL`.
