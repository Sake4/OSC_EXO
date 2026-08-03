API Create/Delete Flask

Gère la création et la suppression sur les tables Groupe et Individu

python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt

Lancer l'API

python app.py

Endpoints

POST  /groupes  Créer un groupe 
DELETE  /groupes/{id_groupe}  Supprimer un groupe (et ses individus, cascade) 
POST  /individus | Créer un individu 
DELETE  /individus/{id_individu}  Supprimer un individu 

Exemple de requête (création d'un groupe)

curl -X POST http://127.0.0.1:8000/groupes \
  -H "Content-Type: application/json" \
  -d '{"numero_groupe": "G1", "id_projet": 1}'
