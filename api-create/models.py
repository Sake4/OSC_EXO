import sqlalchemy as sa
import sqlalchemy.orm as orm
import flask_sqlalchemy as flask_sa

Base = orm.declarative_base()
db = flask_sa.SQLAlchemy(model_class=Base)  # variable à utiliser pour interagir avec la bd

class Encadrant(db.Model):
    __tablename__ = "encadrant"
    id_encadrant = sa.Column(sa.Integer, primary_key=True, index=True)
    nom = sa.Column(sa.String, nullable=False)
    prenom = sa.Column(sa.String, nullable=False)

class Projet(db.Model):
    __tablename__ = "projet"
    id_projet = sa.Column(sa.Integer, primary_key=True, index=True)
    titre_projet = sa.Column(sa.String, nullable=False)
    id_encadrant = sa.Column(sa.Integer, sa.ForeignKey("encadrant.id_encadrant"), nullable=False)

class Groupe(db.Model):
    __tablename__ = "groupe"
    id_groupe = sa.Column(sa.Integer, primary_key=True, index=True)
    numero_groupe = sa.Column(sa.Integer, nullable=False)
    id_projet = sa.Column(sa.Integer, sa.ForeignKey("projet.id_projet"), nullable=False)
    individus = orm.relationship("Individu", back_populates="groupe", cascade="all, delete-orphan")

class Individu(db.Model):
    __tablename__ = "individu"
    id_individu = sa.Column(sa.Integer, primary_key=True, index=True)
    nom = sa.Column(sa.String, nullable=False)
    prenoms = sa.Column(sa.String, nullable=False)
    sexe = sa.Column(sa.String, nullable=False)
    profil = sa.Column(sa.String, nullable=False)
    id_groupe = sa.Column(sa.Integer, sa.ForeignKey("groupe.id_groupe"), nullable=False)
    groupe = orm.relationship("Groupe", back_populates="individus")