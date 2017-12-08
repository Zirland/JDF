#!/bin/bash

echo "Starting..."
mysqladmin -u root -pmedved create JDF2
mysql -u root -pmedved JDF2 < schema.sql


# curl http://localhost/JDF/import_prepare.php

echo "Prepared..."

# ./rozbal.sh

# ./nahraj.sh

echo "Finishing..."

# curl http://localhost/JDF/import_finish.php

echo "Regenerating..."
# curl http://localhost/JDF/regenerate.php

echo "== Konec =="

exit;
