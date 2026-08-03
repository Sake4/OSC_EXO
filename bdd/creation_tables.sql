--table encadrant
CREATE TABLE encadrant (
    id_encadrant SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL
);
--table projet
CREATE TABLE projet (
    id_projet SERIAL PRIMARY KEY,
    titre_projet VARCHAR(255) NOT NULL,
    id_encadrant INT NOT NULL,
    CONSTRAINT fk_projet_encadrant 
        FOREIGN KEY (id_encadrant) 
        REFERENCES encadrant(id_encadrant) 
        ON DELETE RESTRICT ON UPDATE CASCADE
);
--table groupe
CREATE TABLE groupe (
    id_groupe SERIAL PRIMARY KEY,
    numero_groupe INT NOT NULL,
    id_projet INT NOT NULL,
    CONSTRAINT fk_groupe_projet 
        FOREIGN KEY (id_projet) 
        REFERENCES projet(id_projet) 
        ON DELETE RESTRICT ON UPDATE CASCADE
);
--table individu
CREATE TABLE individu (
    id_individu SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(150) NOT NULL,
    sexe CHAR(1) NOT NULL CHECK (sexe IN ('M', 'F')),
    profil VARCHAR(50) NOT NULL CHECK (profil IN ('dev', 'design', 'data/ia','marketing','cybersécurité','mécatronique')),
    id_groupe INT NOT NULL,
    CONSTRAINT fk_individu_groupe 
        FOREIGN KEY (id_groupe) 
        REFERENCES groupe(id_groupe) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

--table de Log
CREATE TABLE log_action (
    id_log SERIAL PRIMARY KEY,
    nom_table VARCHAR(50) NOT NULL,
    action VARCHAR(20) NOT NULL,
    donnee_concernee TEXT NOT NULL,
    date_heure TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--fonction  déclencheur
CREATE OR REPLACE FUNCTION fn_trg_log_action()
RETURNS TRIGGER AS $$
DECLARE
    donnee TEXT;
BEGIN
    IF (TG_OP = 'DELETE') THEN
        donnee := ROW_TO_JSON(OLD)::TEXT;
        INSERT INTO log_action (nom_table, action, donnee_concernee) 
        VALUES (TG_TABLE_NAME, 'DELETE', donnee);
        RETURN OLD;
    ELSIF (TG_OP = 'UPDATE') THEN
        donnee := 'OLD: ' || ROW_TO_JSON(OLD)::TEXT || ' -> NEW: ' || ROW_TO_JSON(NEW)::TEXT;
        INSERT INTO log_action (nom_table, action, donnee_concernee) 
        VALUES (TG_TABLE_NAME, 'UPDATE', donnee);
        RETURN NEW;
    ELSIF (TG_OP = 'INSERT') THEN
        donnee := ROW_TO_JSON(NEW)::TEXT;
        INSERT INTO log_action (nom_table, action, donnee_concernee) 
        VALUES (TG_TABLE_NAME, 'INSERT', donnee);
        RETURN NEW;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;


--Création des déclencheurs sur chaque table
CREATE TRIGGER trg_audit_encadrant
AFTER INSERT OR UPDATE OR DELETE ON encadrant
FOR EACH ROW EXECUTE FUNCTION fn_trg_log_action();

CREATE TRIGGER trg_audit_projet
AFTER INSERT OR UPDATE OR DELETE ON projet
FOR EACH ROW EXECUTE FUNCTION fn_trg_log_action();

CREATE TRIGGER trg_audit_groupe
AFTER INSERT OR UPDATE OR DELETE ON groupe
FOR EACH ROW EXECUTE FUNCTION fn_trg_log_action();

CREATE TRIGGER trg_audit_individu
AFTER INSERT OR UPDATE OR DELETE ON individu
FOR EACH ROW EXECUTE FUNCTION fn_trg_log_action();


--Insertions
INSERT INTO encadrant (nom, prenom) VALUES
('ALLOU', 'Stéphane'),
('SITTIE', 'Erwin Frédéric');

INSERT INTO projet (titre_projet, id_encadrant) VALUES
('Système de gestion et optimisation de la consomation carburant', 1),
('CyberShield - Anti SIM Swap', 1),
('Robot dassistance clients en agence', 2),
('Détection de section de câble électrique défaillant', 2);

INSERT INTO groupe (numero_groupe, id_projet) VALUES
(1, 1),
(2, 2), 
(3, 3),
(4, 4); 

INSERT INTO individu (nom, prenoms, sexe, profil, id_groupe) VALUES
('TANO', 'Paul Yoris', 'M', 'design', 2),
('DAYO', 'Aïchata', 'F', 'marketing', 1),
('FRONDO', 'Paul-Elie', 'M', 'mécatronique', 1),
('YENDOUKOA', 'Noel', 'M', 'cybersécurité', 1),
('EKRA', 'Ama Esther Marie Grace Divine', 'F', 'marketing', 2),
('ASSIENIN', 'Evra Franklin Koffi', 'M', 'mécatronique', 2),
('GOABI', 'Yves Desiré', 'M', 'design', 3),
('DIATA', 'Rachelle', 'F', 'design', 4),
('DEME', 'Abibata', 'F', 'marketing', 4);

SELECT * FROM log_action ORDER BY date_heure DESC;