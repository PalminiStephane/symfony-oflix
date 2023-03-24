# création du projet
composer create-project symfony/skeleton project
mv project/* project/.* .
rmdir project/
# E02 : twig
composer require annotations
composer require twig
# E03 : twig et debug
composer require symfony/asset
composer require --dev symfony/profiler-pack
composer require --dev symfony/var-dumper
composer require --dev symfony/debug-bundle
composer require --dev symfony/maker-bundle
# E05 : doctrine
composer require symfony/orm-pack
composer require --dev orm-fixtures
# E09 : formulaires
composer require symfony/form
composer require symfony/validator

# E09 : make:crud
composer require security-csrf
