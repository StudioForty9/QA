mv behat.yml.sample behat.yml

cd ./features/

for file in *.sample; do
  mv "$file" "`basename $file .sample`"
done

mv bootstrap/MagentoProjectContext.php.sample bootstrap/MagentoProjectContext.php

