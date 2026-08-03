const express = require("express");
const pool = require("./db");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());

//GROUPE 
app.get("/groupes", async (req, res) => {
  try {
    const result = await pool.query("SELECT * FROM groupe ORDER BY id_groupe");
    res.json(result.rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

app.get("/groupes/:id", async (req, res) => {
  try {
    const result = await pool.query(
      "SELECT * FROM groupe WHERE id_groupe = $1",
      [req.params.id]
    );
    if (result.rows.length === 0) {
      return res.status(404).json({ error: "Groupe introuvable." });
    }
    res.json(result.rows[0]);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

app.put("/groupes/:id", async (req, res) => {
  const { numero_groupe, id_projet } = req.body;
  if (!numero_groupe || !id_projet) {
    return res
      .status(400)
      .json({ error: "numero_groupe et id_projet sont requis." });
  }

  try {
    const result = await pool.query(
      `UPDATE groupe
       SET numero_groupe = $1, id_projet = $2
       WHERE id_groupe = $3
       RETURNING *`,
      [numero_groupe, id_projet, req.params.id]
    );
    if (result.rows.length === 0) {
      return res.status(404).json({ error: "Groupe introuvable." });
    }
    res.json(result.rows[0]);
  } catch (err) {
    if (err.code === "23503") {
      return res
        .status(400)
        .json({ error: "id_projet invalide : le projet référencé n'existe pas." });
    }
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

//INDIVIDU
app.get("/individus", async (req, res) => {
  try {
    const result = await pool.query("SELECT * FROM individu ORDER BY id_individu");
    res.json(result.rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

app.get("/individus/:id", async (req, res) => {
  try {
    const result = await pool.query(
      "SELECT * FROM individu WHERE id_individu = $1",
      [req.params.id]
    );
    if (result.rows.length === 0) {
      return res.status(404).json({ error: "Individu introuvable." });
    }
    res.json(result.rows[0]);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

app.put("/individus/:id", async (req, res) => {
  const { nom, prenoms, sexe, profil, id_groupe } = req.body;
  if (!nom || !prenoms || !sexe || !profil || !id_groupe) {
    return res.status(400).json({
      error: "nom, prenoms, sexe, profil et id_groupe sont requis.",
    });
  }

  try {
    const result = await pool.query(
      `UPDATE individu
       SET nom = $1, prenoms = $2, sexe = $3, profil = $4, id_groupe = $5
       WHERE id_individu = $6
       RETURNING *`,
      [nom, prenoms, sexe, profil, id_groupe, req.params.id]
    );
    if (result.rows.length === 0) {
      return res.status(404).json({ error: "Individu introuvable." });
    }
    res.json(result.rows[0]);
  } catch (err) {
    if (err.code === "23503") {
      return res
        .status(400)
        .json({ error: "id_groupe invalide : le groupe référencé n'existe pas." });
    }
    console.error(err);
    res.status(500).json({ error: "Erreur serveur." });
  }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`API Read/Update lancée sur http://127.0.0.1:${PORT}`);
});