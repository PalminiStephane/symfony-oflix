
# Scripts

ici nous mettrons des scripts pour nous faciliter la vie.

Il faut donner le droit d'éxécution à chacun des scripts pour pouvoir les executer

```bash
chmod +x ./create_404_custom.sh
```

## maj_composer_v2.5.3.sh

Permet d'installer la version 2.5.3 de composer sur notre poste

⚠️ ce script ne fonctionne que pour passer en version 2.5.3. Si une nouvelle version sort, le script ne fonctionnera plus, à cause de la deuxième commande.
Il faudrat retourner sur le [site officiel](https://getcomposer.org/download/) et prendre les nouvelles commandes.

## create_project_symfo.sh

On va mettre dans ce script, tout les commandes qui nous permette de bien démarrer un projet symfo à partir du skeleton.

### création du skeleton

```bash
composer create-project symfony/skeleton project
mv project/* project/.* .
rmdir project/
```
