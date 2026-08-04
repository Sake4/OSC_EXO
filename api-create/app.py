import os
from dotenv import load_dotenv
from flask import Flask, request, jsonify
from sqlalchemy.exc import IntegrityError
from models import Projet, db, Groupe, Individu, Encadrant
from flask_cors import CORS

load_dotenv()

app = Flask(__name__)
CORS(app)
app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv("DATABASE_URL")
db.init_app(app)

# ENCADRANTS
@app.route("/encadrants", methods=["POST"])
def create_encadrant():
    data = request.get_json()
    if not data or "nom" not in data or "prenom" not in data:
        return jsonify({"error": "nom et prenom sont requis."}), 400

    nouveau_encadrant = Encadrant(
        nom=data["nom"],
        prenom=data["prenom"]
    )
    db.session.add(nouveau_encadrant)
    try:
        db.session.commit()
    except IntegrityError:
        db.session.rollback()
        return jsonify({"error": "Erreur lors de la création de l'encadrant."}), 400

    return jsonify({
        "id_encadrant": nouveau_encadrant.id_encadrant,
        "nom": nouveau_encadrant.nom,
        "prenom": nouveau_encadrant.prenom
    }), 201

@app.route("/encadrants/<int:id_encadrant>", methods=["DELETE"])
def delete_encadrant(id_encadrant):
    encadrant = db.session.get(Encadrant, id_encadrant)
    if not encadrant:
        return jsonify({"error": "Encadrant introuvable."}), 404

    db.session.delete(encadrant)
    db.session.commit()
    return "", 204

# PROJETS
@app.route("/projets", methods=["POST"])
def create_projet():
    data = request.get_json()
    if not data or "titre_projet" not in data or "id_encadrant" not in data:
        return jsonify({"error": "titre_projet et id_encadrant sont requis."}), 400

    nouveau_projet = Projet(
        titre_projet=data["titre_projet"],
        id_encadrant=data["id_encadrant"]
    )
    db.session.add(nouveau_projet)
    try:
        db.session.commit()
    except IntegrityError:
        db.session.rollback()
        return jsonify({"error": "id_encadrant invalide : l'encadrant référencé n'existe pas."}), 400

    return jsonify({
        "id_projet": nouveau_projet.id_projet,
        "titre_projet": nouveau_projet.titre_projet,
        "id_encadrant": nouveau_projet.id_encadrant
    }), 201

@app.route("/projets/<int:id_projet>", methods=["DELETE"])
def delete_projet(id_projet):
    projet = db.session.get(Projet, id_projet)
    if not projet:
        return jsonify({"error": "Projet introuvable."}), 404

    db.session.delete(projet)  # cascade supprime aussi les individus liés
    db.session.commit()
    return "", 204

# GROUPES
@app.route("/groupes", methods=["POST"])
def create_groupe():
    data = request.get_json()
    if not data or "numero_groupe" not in data or "id_projet" not in data:
        return jsonify({"error": "numero_groupe et id_projet sont requis."}), 400

    nouveau_groupe = Groupe(
        numero_groupe=data["numero_groupe"],
        id_projet=data["id_projet"]
    )
    db.session.add(nouveau_groupe)
    try:
        db.session.commit()
    except IntegrityError:
        db.session.rollback()
        return jsonify({"error": "id_projet invalide : le projet référencé n'existe pas."}), 400

    return jsonify({
        "id_groupe": nouveau_groupe.id_groupe,
        "numero_groupe": nouveau_groupe.numero_groupe,
        "id_projet": nouveau_groupe.id_projet
    }), 201

@app.route("/groupes/<int:id_groupe>", methods=["DELETE"])
def delete_groupe(id_groupe):
    groupe = db.session.get(Groupe, id_groupe)
    if not groupe:
        return jsonify({"error": "Groupe introuvable."}), 404

    db.session.delete(groupe)  # cascade supprime aussi les individus liés
    db.session.commit()
    return "", 204

# INDIVIDUS
@app.route("/individus", methods=["POST"])
def create_individu():
    data = request.get_json()
    champs_requis = ["nom", "prenoms", "sexe", "profil", "id_groupe"]
    if not data or not all(champ in data for champ in champs_requis):
        return jsonify({"error": f"Champs requis : {', '.join(champs_requis)}"}), 400

    nouvel_individu = Individu(
        nom=data["nom"],
        prenoms=data["prenoms"],
        sexe=data["sexe"],
        profil=data["profil"],
        id_groupe=data["id_groupe"]
    )
    db.session.add(nouvel_individu)
    try:
        db.session.commit()
    except IntegrityError:
        db.session.rollback()
        return jsonify({"error": "id_groupe invalide : le groupe référencé n'existe pas."}), 400

    return jsonify({
        "id_individu": nouvel_individu.id_individu,
        "nom": nouvel_individu.nom,
        "prenoms": nouvel_individu.prenoms,
        "sexe": nouvel_individu.sexe,
        "profil": nouvel_individu.profil,
        "id_groupe": nouvel_individu.id_groupe
    }), 201


@app.route("/individus/<int:id_individu>", methods=["DELETE"])
def delete_individu(id_individu):
    individu = db.session.get(Individu, id_individu)
    if not individu:
        return jsonify({"error": "Individu introuvable."}), 404

    db.session.delete(individu)
    db.session.commit()
    return "", 204


if __name__ == "__main__":
    app.run(debug=True, use_reloader=False)