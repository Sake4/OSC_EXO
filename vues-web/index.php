<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$message = null;
$erreur = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['action_type'] ?? '';

    switch ($type) {
        case 'creer_encadrant':
            [$succes, $err] = apiPost(API_CREATE . '/encadrants', [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
            ]);
            $msg = $succes ? 'Encadrant créé avec succès.' : $err;
            break;
        case 'creer_projet':
            [$succes, $err] = apiPost(API_CREATE . '/projets', [
                'titre_projet' => $_POST['titre_projet'],
                'id_encadrant' => (int) $_POST['id_encadrant'],
            ]);
            $msg = $succes ? 'Projet créé avec succès.' : $err;
            break;
        case 'creer_groupe':
            [$succes, $err] = apiPost(API_CREATE . '/groupes', [
                'numero_groupe' => (int) $_POST['numero_groupe'],
                'id_projet' => (int) $_POST['id_projet'],
            ]);
            $msg = $succes ? 'Groupe créé avec succès.' : $err;
            break;

        case 'creer_individu':
            [$succes, $err] = apiPost(API_CREATE . '/individus', [
                'nom' => $_POST['nom'],
                'prenoms' => $_POST['prenoms'],
                'sexe' => $_POST['sexe'],
                'profil' => $_POST['profil'],
                'id_groupe' => (int) $_POST['id_groupe'],
            ]);
            $msg = $succes ? 'Individu créé avec succès.' : $err;
            break;

        case 'modifier_encadrant':
            $id = (int) $_POST['id_encadrant'];
            [$succes, $err] = apiPut(API_READ_UPDATE . "/encadrants/$id", [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
            ]);
            $msg = $succes ? 'Encadrant modifié avec succès.' : $err;
            break;

        case 'modifier_projet':
            $id = (int) $_POST['id_projet'];
            [$succes, $err] = apiPut(API_READ_UPDATE . "/projets/$id", [
                'titre_projet' => $_POST['titre_projet'],
                'id_encadrant' => (int) $_POST['id_encadrant'],
            ]);
            $msg = $succes ? 'Projet modifié avec succès.' : $err;
            break;

        case 'modifier_groupe':
            $id = (int) $_POST['id_groupe'];
            [$succes, $err] = apiPut(API_READ_UPDATE . "/groupes/$id", [
                'numero_groupe' => (int) $_POST['numero_groupe'],
                'id_projet' => (int) $_POST['id_projet'],
            ]);
            $msg = $succes ? 'Groupe modifié avec succès.' : $err;
            break;

        case 'modifier_individu':
            $id = (int) $_POST['id_individu'];
            [$succes, $err] = apiPut(API_READ_UPDATE . "/individus/$id", [
                'nom' => $_POST['nom'],
                'prenoms' => $_POST['prenoms'],
                'sexe' => $_POST['sexe'],
                'profil' => $_POST['profil'],
                'id_groupe' => (int) $_POST['id_groupe'],
            ]);
            $msg = $succes ? 'Individu modifié avec succès.' : $err;
            break;

        default:
            $msg = 'Action inconnue.';
            $succes = false;
    }
    header('Location: index.php?message=' . urlencode($msg) . '&erreur=' . ($succes ? '0' : '1'));
    exit;
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $erreur = $_GET['erreur'] === '1';
}

$encadrants = apiGet(API_READ_UPDATE . '/encadrants');
$projets = apiGet(API_READ_UPDATE . '/projets');
$groupes = apiGet(API_READ_UPDATE . '/groupes');
$individus = apiGet(API_READ_UPDATE . '/individus');
?>


<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion de groupes — Création &amp; modification (POST natif)</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; color: #1c2321; }
  h1 { font-size: 1.5rem; }
  h2 { font-size: 1.1rem; margin-top: 2.5rem; border-bottom: 1px solid #ddd; padding-bottom: 0.4rem; }
  form.inline { display: inline-flex; gap: 0.4rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; }
  form.creation { display: flex; flex-direction: column; gap: 0.6rem; max-width: 380px; margin-bottom: 1.5rem; }
  input, select { padding: 0.4rem; border: 1px solid #ccc; border-radius: 4px; }
  button { padding: 0.4rem 0.9rem; border: none; border-radius: 4px; background: #2f6f5e; color: #fff; cursor: pointer; }
  button:hover { background: #234f43; }
  .alert { padding: 0.7rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
  .alert.ok { background: #e3f3ec; color: #1e5e46; }
  .alert.err { background: #fbe9e6; color: #8a2f22; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
  td, th { padding: 0.4rem 0.6rem; border-bottom: 1px solid #eee; font-size: 0.9rem; text-align: left; }
</style>
</head>
<body>

<h1>Gestion de groupes — Création &amp; modification</h1>
<p><em>Formulaires HTML natifs, rechargement complet de page à chaque soumission (pas d'AJAX).</em></p>

<?php if ($message): ?>
  <div class="alert <?= $erreur ? 'err' : 'ok' ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h2>Créer un encadrant</h2>
<form class="creation" method="POST" action="index.php">
  <input type="hidden" name="action_type" value="creer_encadrant">
  <input type="text" name="nom" placeholder="Nom" required>
  <input type="text" name="prenom" placeholder="Prénom" required>
  <button type="submit">Créer l'encadrant</button>
</form>

<h2>Créer un projet</h2>
<form class="creation" method="POST" action="index.php">
  <input type="hidden" name="action_type" value="creer_projet">
  <input type="text" name="titre_projet" placeholder="Titre du projet" required>
  <input type="number" name="id_encadrant" placeholder="ID Encadrant" required>
  <button type="submit">Créer le projet</button>
</form>

<h2>Créer un groupe</h2>
<form class="creation" method="POST" action="index.php">
  <input type="hidden" name="action_type" value="creer_groupe">
  <input type="number" name="numero_groupe" placeholder="Numéro de groupe (ex: G1)" required>
  <input type="number" name="id_projet" placeholder="ID Projet" required>
  <button type="submit">Créer le groupe</button>
</form>

<h2>Créer un individu</h2>
<form class="creation" method="POST" action="index.php">
  <input type="hidden" name="action_type" value="creer_individu">
  <input type="text" name="nom" placeholder="Nom" required>
  <input type="text" name="prenoms" placeholder="Prénoms" required>
  <select name="sexe" required>
    <option value="">-- Sexe --</option>
    <option value="M">M</option>
    <option value="F">F</option>
  </select>
  <select name="profil" required>
    <option value="">-- Profil --</option>
    <option value="dev">Développeur</option>
    <option value="design">Designer</option>
    <option value="marketing">Marketing</option>
    <option value="data/ia">Data/IA</option>
    <option value="cybersécurité">Cybersécurité</option>
    <option value="mécatronique">Mécatronique</option>
  </select>
  <input type="number" name="id_groupe" placeholder="ID Groupe" required>
  <button type="submit">Créer l'individu</button>
</form>

<h2>Modifier un encadrant existant</h2>
<table>
  <thead><tr><th>ID</th><th colspan="3">Modifier</th></tr></thead>
  <tbody>
    <?php foreach ($encadrants as $e): ?>
    <tr>
      <td><?= htmlspecialchars($e['id_encadrant']) ?></td>
      <td colspan="3">
        <form class="inline" method="POST" action="index.php">
          <input type="hidden" name="action_type" value="modifier_encadrant">
          <input type="hidden" name="id_encadrant" value="<?= htmlspecialchars($e['id_encadrant']) ?>">
          <input type="text" name="nom" value="<?= htmlspecialchars($e['nom']) ?>" required>
          <input type="text" name="prenom" value="<?= htmlspecialchars($e['prenom']) ?>" required>
          <button type="submit">Enregistrer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Modifier un projet existant</h2>
<table>
  <thead><tr><th>ID</th><th colspan="3">Modifier</th></tr></thead>
  <tbody>
    <?php foreach ($projets as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['id_projet']) ?></td>
      <td colspan="3">
        <form class="inline" method="POST" action="index.php">
          <input type="hidden" name="action_type" value="modifier_projet">
          <input type="hidden" name="id_projet" value="<?= htmlspecialchars($p['id_projet']) ?>">
          <input type="text" name="titre_projet" value="<?= htmlspecialchars($p['titre_projet']) ?>" required>
          <input type="number" name="id_encadrant" value="<?= htmlspecialchars($p['id_encadrant']) ?>" required>
          <button type="submit">Enregistrer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Modifier un groupe existant</h2>
<table>
  <thead><tr><th>ID</th><th colspan="3">Modifier</th></tr></thead>
  <tbody>
    <?php foreach ($groupes as $g): ?>
    <tr>
      <td><?= htmlspecialchars($g['id_groupe']) ?></td>
      <td colspan="3">
        <form class="inline" method="POST" action="index.php">
          <input type="hidden" name="action_type" value="modifier_groupe">
          <input type="hidden" name="id_groupe" value="<?= htmlspecialchars($g['id_groupe']) ?>">
          <input type="number" name="numero_groupe" value="<?= htmlspecialchars($g['numero_groupe']) ?>" required>
          <input type="number" name="id_projet" value="<?= htmlspecialchars($g['id_projet']) ?>" required>
          <button type="submit">Enregistrer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Modifier un individu existant</h2>
<table>
  <thead><tr><th>ID</th><th colspan="5">Modifier</th></tr></thead>
  <tbody>
    <?php foreach ($individus as $i): ?>
    <tr>
      <td><?= htmlspecialchars($i['id_individu']) ?></td>
      <td colspan="5">
        <form class="inline" method="POST" action="index.php">
          <input type="hidden" name="action_type" value="modifier_individu">
          <input type="hidden" name="id_individu" value="<?= htmlspecialchars($i['id_individu']) ?>">
          <input type="text" name="nom" value="<?= htmlspecialchars($i['nom']) ?>" required>
          <input type="text" name="prenoms" value="<?= htmlspecialchars($i['prenoms']) ?>" required>
          <select name="sexe" required>
            <option value="M" <?= $i['sexe'] === 'M' ? 'selected' : '' ?>>M</option>
            <option value="F" <?= $i['sexe'] === 'F' ? 'selected' : '' ?>>F</option>
          </select>
          <select name="profil" required>
            <option value="">-- Profil --</option>
            <option value="dev" <?= $i['profil'] === 'dev' ? 'selected' : '' ?>>Développeur</option>
            <option value="design" <?= $i['profil'] === 'design' ? 'selected' : '' ?>>Designer</option>
            <option value="marketing" <?= $i['profil'] === 'marketing' ? 'selected' : '' ?>>Marketing</option>
            <option value="data/ia" <?= $i['profil'] === 'data/ia' ? 'selected' : '' ?>>Data/IA</option>
            <option value="cybersécurité" <?= $i['profil'] === 'cybersécurité' ? 'selected' : '' ?>>Cybersécurité</option>
            <option value="mécatronique" <?= $i['profil'] === 'mécatronique' ? 'selected' : '' ?>>Mécatronique</option>
          </select>
          <input type="number" name="id_groupe" value="<?= htmlspecialchars($i['id_groupe']) ?>" required>
          <button type="submit">Enregistrer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
