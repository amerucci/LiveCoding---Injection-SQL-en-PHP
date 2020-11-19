<?php

function connect() //fonction de connextion à la base
    {
        try
        {
            $bdd = new PDO('mysql:host=localhost;dbname=failledesecurite;port=3306;charset=utf8', 'root', '');
            return $bdd;
         
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
    }

    ?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injection SQL en PHP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>
<body>
<div class="container">
<h1 class="my-5">Comment éviter une injection SQL en PHP ?</h1>
<p>Les injections SQL sont une technique courante pour récupérer des informations contenues dans une base de données ou endommager un site internet. Elle consiste à insérer du code SQL dans des champs. Lorsque ces champs sont ensuite insérés en base, le code SQL est interprété et une nouvelle requête est envoyée à la base de données, ce qui permet d'obtenir, de modifier ou de supprimer des informations. Le langage PHP permet heureusement de se protéger contre ce type d'attaque. </p>

<section id="avecquery">
<h2 class="mb-3">Affichage d'une requete avec un query</h2>
<p><code>
query('SELECT * From Users')</code></p>
<div class="jumbotron">
<?php
// ici on va lancer notre requette pour afficher toutes les informations de la base user


    $requette = connect()->query('SELECT * From Users');
    $requette=$requette->fetchAll();
    echo "<table class='table'>
    <thead>
    <tr>
      <th scope='col'>ID</th>
      <th scope='col'>Username</th>
      <th scope='col'>Password</th>
      <th scope='col'>Birth Date</th>
    </tr>
  </thead>";
    foreach ($requette as $info){
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['username']."</td>";
        echo "<td>".$info['passwd']."</td>";
        echo "<td>".$info['info']."</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
</div>
</section>
<section><p>Maintenant refaisons la même chose mais en ajoutant la condition que l'utilisateur doit être Gantt</p>
<p><code>query('SELECT * From Users WHERE username="Gantt"')</code></p>
<div class="jumbotron">
<?php
$requette = connect()->query('SELECT * From Users WHERE username="Gantt"');
$requette=$requette->fetchAll();
echo "<table class='table'>
    <thead>
    <tr>
      <th scope='col'>ID</th>
      <th scope='col'>Username</th>
      <th scope='col'>Password</th>
      <th scope='col'>Birth Date</th>
    </tr>
  </thead>";
    foreach ($requette as $info){
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['username']."</td>";
        echo "<td>".$info['passwd']."</td>";
        echo "<td>".$info['info']."</td>";
        echo "</tr>";
    }
    echo "</table>";
//Très bien on voit que cela fonctionne qu'il ne me retourne que les informations quand l'identifiant est égale à Gantt
//Mais maintenant on va voir comment une personne mal intentionnée pourrait faire pour afficher toutes les informations même si l'on ne veut afficher que celle concernant Gantt à la base
//Pour ce faire on va faire en sorte que la variable soit issue d'un formulaire
// première étape on va se créer un petit formulaire en disant je veut afficher les informations de tel ou tel utilisateur.
?>
</div>
</section>
<section id="select">
<h2 class="mb-3">On fait un select qui va afficher les info d'un utilisateur particulier</h2>

<div class="jumbotron">

<form method="post">
<div class="input-group">
<select name="personneselectionnee" class="custom-select">
<option value="1">Gantt</option>
<option value="2">Dixon</option>
<input type="submit" value="Envoyer"  class="btn btn-outline-secondary">
</div>
</select>
</form>

<?php

if(isset($_REQUEST['personneselectionnee'])){
$personne = $_REQUEST['personneselectionnee'];
$requette = connect()->query('SELECT * From Users WHERE id="'.$personne.'"');
$requette=$requette->fetchAll(PDO::FETCH_ASSOC);
echo "<table class='table'>
    <thead>
    <tr>
      <th scope='col'>ID</th>
      <th scope='col'>Username</th>
      <th scope='col'>Password</th>
      <th scope='col'>Birth Date</th>
    </tr>
  </thead>";
    foreach ($requette as $info){
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['username']."</td>";
        echo "<td>".$info['passwd']."</td>";
        echo "<td>".$info['info']."</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
<p>Tres bien là encore cela fonctionne</p>
<p>Maintenant imaginez que notre méchant pirate arrive à remplacer le contenu de notre variable selectionné par <code>  $personne = '" OR 1=1#"';</code> <p>

<div class="jumbotron">

<form method="post">
<div class="input-group">
<select name="personneselectionneepassecure" class="custom-select">
<option value="1">Gantt</option>
<option value="2">Dixon</option>
<input type="submit" value="Envoyer"  class="btn btn-outline-secondary">
</div>
</select>
</form>

<?php

if(isset($_REQUEST['personneselectionneepassecure'])){
    $personne = '" OR 1=1#"';
$requette = connect()->query('SELECT * From Users WHERE id="'.$personne.'"');
$requette=$requette->fetchAll(PDO::FETCH_ASSOC);
echo "<table class='table'>
    <thead>
    <tr>
      <th scope='col'>ID</th>
      <th scope='col'>Username</th>
      <th scope='col'>Password</th>
      <th scope='col'>Birth Date</th>
    </tr>
  </thead>";
    foreach ($requette as $info){
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['username']."</td>";
        echo "<td>".$info['passwd']."</td>";
        echo "<td>".$info['info']."</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
<p>La vous voyez que même si l'on selectionne une personne qu'on récupère son id et que l'on fait une requette la dessus, il s'en tape il affiche tout car en gros le mechant hacker a dit, tu te moques de la condition selectionnée, utilise juste la mienne qui dit que 1 est egale à 1 et que tout ce qui vient après soit commenté et ce grâce au #. Alors pour palier à cela il éxiste une fonction php qui est bien utile qui s'appelle le <code>htmlspecialchars</code> qui convertit les caractères spéciaux en entités HTML</p>
<p>Maintenant essayons</p>
<div class="jumbotron">

<form method="post">
<div class="input-group">
<select name="personneselectionneesecure" class="custom-select">
<option value="1">Gantt</option>
<option value="2">Dixon</option>
<input type="submit" value="Envoyer"  class="btn btn-outline-secondary">
</div>
</select>
</form>

<?php

if(isset($_REQUEST['personneselectionneesecure'])){
    $personne = htmlspecialchars(" OR 1=1#");
$requette = connect()->query('SELECT * From Users WHERE id="'.$personne.'"');
$requette=$requette->fetchAll(PDO::FETCH_ASSOC);
echo "<table class='table'>
    <thead>
    <tr>
      <th scope='col'>ID</th>
      <th scope='col'>Username</th>
      <th scope='col'>Password</th>
      <th scope='col'>Birth Date</th>
    </tr>
  </thead>";
    foreach ($requette as $info){
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['username']."</td>";
        echo "<td>".$info['passwd']."</td>";
        echo "<td>".$info['info']."</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
<p>
On constate que cela fonctionne cependant on favorisera donc l'utilisation des méthodes prepare et excecute de PDO qui elles se chargent déjà de toutes les notions de sécurité au niveau de l'injection. L'utilisation de <code>htmlspecialchars</code> n'est pas forcément optimale puisqu'elle lèze les utilisateur. On ne sais jamais si il fallait vraiment qu'ils envoient des caractères spéciaux class
</p>
</section>

<section class="mt-5 alert-success p-5">
<h3 class="mb-5" >Conclusion</h3>
<p>Lorsque l'on effectue une requête de manière simple (query), les données sont directement interprétées. Il est possible par exemple pour une recherche d'insérer du code malicieux.</p>
<p>Pour prévenir les injections SQL, il faut faire appel aux requêtes préparées. Ce sont des requêtes dans lesquels les paramètres sont interprétés indépendamment de la requête elle-même. De cette manière, il est impossible d'effectuer des injections. Dans tous les systèmes de gestion de bases de données, deux méthodes sont utilisées : prepare() qui prépare la requête et execute() qui exécute la requête avec les paramètres.</p>
<p>Si vous utilisez PDO sur une base de données MySQL, il faut savoir que par défaut les requêtes préparées ne sont pas réelles, elles sont émulées par PDO. </p>
</section>


</div>
</body>
</html>


