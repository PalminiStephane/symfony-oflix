if [ -z "$1" ]
then
      echo "\$1 is NULL"
else
    day=$1
    cd ../cours
    touch "${day}.md"
    echo "# ${day}" >> "${day}.md"
    cd ../commandes
    touch "${day}.md"
    echo "# ${day}" >> "${day}.md"
    cd ../erreurs
    touch "${day}.md"
    echo "# ${day}" >> "${day}.md"
fi