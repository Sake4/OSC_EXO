import os
import xml.etree.ElementTree as ET
from xml.dom import minidom

import psycopg2
import psycopg2.extras
from dotenv import load_dotenv

load_dotenv()

DATABASE_URL = os.getenv("DATABASE_URLE")

def recuperer_donnees():
    """Récupère les 4 tables en une seule connexion et les renvoie sous forme de listes."""
    conn = psycopg2.connect(DATABASE_URL)
    cur = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

    cur.execute("SELECT * FROM encadrant ORDER BY id_encadrant")
    encadrants = cur.fetchall()

    cur.execute("SELECT * FROM projet ORDER BY id_projet")
    projets = cur.fetchall()

    cur.execute("SELECT * FROM groupe ORDER BY id_groupe")
    groupes = cur.fetchall()

    cur.execute("SELECT * FROM individu ORDER BY id_individu")
    individus = cur.fetchall()

    cur.close()
    conn.close()
    return encadrants, projets, groupes, individus


def construire_xml(encadrants, projets, groupes, individus):
    """Construit l'arbre XML en imbriquant chaque niveau selon les clés étrangères."""

    projets_par_encadrant = {}
    for p in projets:
        projets_par_encadrant.setdefault(p["id_encadrant"], []).append(p)

    groupes_par_projet = {}
    for g in groupes:
        groupes_par_projet.setdefault(g["id_projet"], []).append(g)

    individus_par_groupe = {}
    for i in individus:
        individus_par_groupe.setdefault(i["id_groupe"], []).append(i)

    racine = ET.Element("export")

    for enc in encadrants:
        el_encadrant = ET.SubElement(racine, "encadrant", id=str(enc["id_encadrant"]))
        ET.SubElement(el_encadrant, "nom").text = enc["nom"]
        ET.SubElement(el_encadrant, "prenom").text = enc["prenom"]

        el_projets = ET.SubElement(el_encadrant, "projets")
        for proj in projets_par_encadrant.get(enc["id_encadrant"], []):
            el_projet = ET.SubElement(el_projets, "projet", id=str(proj["id_projet"]))
            ET.SubElement(el_projet, "titre_projet").text = proj["titre_projet"]

            el_groupes = ET.SubElement(el_projet, "groupes")
            for grp in groupes_par_projet.get(proj["id_projet"], []):
                el_groupe = ET.SubElement(el_groupes, "groupe", id=str(grp["id_groupe"]))
                ET.SubElement(el_groupe, "numero_groupe").text = str(grp["numero_groupe"])

                el_individus = ET.SubElement(el_groupe, "individus")
                for ind in individus_par_groupe.get(grp["id_groupe"], []):
                    el_individu = ET.SubElement(
                        el_individus, "individu", id=str(ind["id_individu"])
                    )
                    ET.SubElement(el_individu, "nom").text = ind["nom"]
                    ET.SubElement(el_individu, "prenoms").text = ind["prenoms"]
                    ET.SubElement(el_individu, "sexe").text = ind["sexe"]
                    ET.SubElement(el_individu, "profil").text = ind["profil"]

    return racine


def sauvegarder_xml(racine, chemin="export.xml"):
    """Sérialise l'arbre XML en fichier, avec indentation lisible."""
    xml_brut = ET.tostring(racine, encoding="utf-8")
    xml_joli = minidom.parseString(xml_brut).toprettyxml(indent="  ")
    with open(chemin, "w", encoding="utf-8") as f:
        f.write(xml_joli)


if __name__ == "__main__":
    encadrants, projets, groupes, individus = recuperer_donnees()
    racine = construire_xml(encadrants, projets, groupes, individus)
    sauvegarder_xml(racine)
    print(f"Export terminé : export.xml généré ({len(encadrants)} encadrant(s), "
          f"{len(projets)} projet(s), {len(groupes)} groupe(s), "
          f"{len(individus)} individu(s)).")